<?php
// src\MB\ResourceBundle\Entity\ResourceListener;
namespace VMB\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VMB\ResourceBundle\Entity\Resource;
use \GetId3\GetId3Core as GetId3;
use Exception;

class ResourceListener
{
    /**
     * preUpload
     */
    public function prePersist(Resource $resource, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        if (null !== $resource->file)
        {
        	// TODO: Filter/match authorized & unauthorized extensions? 
            // Binary code could be injected in images
        	// so can't be sure no attack vectors included at all.
            $extension = strtolower(pathinfo($resource->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $resource->setExtension($extension);
            $resource->mime_type = explode("/",$resource->file->getMimeType());
            $primary_typ = $resource->mime_type[0];
            $secondary_typ = $resource->mime_type[1];

            // Taking some elementary precautions.
            if (null === $resource->mime_type){
            	$em->detach($resource);
            	throw new Exception(" Unrecognized media type.", 1);
            } elseif($primary_typ == 'application' && $secondary_typ != 'pdf'){
            	$em->detach($resource);
                throw new Exception(" Unauthorized file type.", 1);
            } elseif($primary_typ == 'application' && $secondary_typ == 'pdf'){
            	$resource->setType($secondary_typ);
            } else {
            	$resource->setType($primary_typ);
            }

            $resource->setSize(filesize($resource->file));
            $resource->setPath("");
           
           /*Analyse du fichier avec getID3 */
           $getId3 = new GetId3();
           $analyse = $getId3
                ->setOptionMD5Data(true)
                ->setOptionMD5DataSource(true)
                ->setEncoding('UTF-8')
                ->analyze($resource->file);
               
            if(in_array($extension, array('jpeg', 'jpg', 'png'))){
                
                $resource->setHeight($analyse['video']['resolution_y']);
                $resource->setWidth($analyse['video']['resolution_x']);
            }
            elseif(in_array($extension, array('ogg', 'mp3'))){
                $resource->setDuration($analyse['playtime_seconds']);
            }
            elseif($resource->getType() == 'video'){
                $resource->setDuration($analyse['playtime_seconds']);   
            } 
        } else {
        	throw new Exception("File transmission error", 1);
        }
    }
    
    /**
     * upload
     */
    public function postPersist(Resource $resource, LifecycleEventArgs $args)
    {
        if (null === $resource->file){
            return;
        }
        $extension = $resource->getExtension();

    /*    
        Lancer une exception ici si le fichier ne peut pas
        être déplacé afin que l'entité ne soit pas persistée dans la
        base de données
    */
    	try {
	    	// If directories do not exist yet, create them 
            // (default/no-arg mode : 777)
	        if (!is_dir($resource->getUploadRootDir($resource->getType())))
	        {
	            if (!is_dir($resource->getUploadRootDir())) {
	            	try {
	                	mkdir($resource->getUploadRootDir());	
	            	} catch (IOException $e) {
	            		return $e->getMessage();
	            	}
	            }
	            try {
	            	mkdir($resource->getUploadRootDir($resource->getType()));	
	            } catch (IOException $e) {
	            	return $e->getMessage();
	            }
	        }
	        $resource->file->move($resource->getUploadRootDir($resource->getType()), 
                $resource->getFilename().'.'.$resource->getExtension());
	        unset($resource->file);     	
    	} catch (FileException $e) {
    		$em = $args->getEntityManager();
    		$em->detach($resource);
    		return $e->getMessage();
    	}


        if(in_array($resource->getExtension(), array('jpeg', 'jpg', 'png'))){
            // on crée la miniature
            // http://php.net/manual/en/function.imagecreate.php
            if($extension =="jpg" || $extension =="jpeg" ){

                $uploadedfile = $resource->getUploadRootDir($resource->getType()).$resource->getFilename().'.'.$resource->getExtension();
                $src = imagecreatefromjpeg($uploadedfile);
            }
            else if($extension=="png"){
                $uploadedfile = $resource->getUploadRootDir($resource->getType()).$resource->getFilename().'.'.$resource->getExtension();
                $src = imagecreatefrompng($uploadedfile);
            }
 
            list($width,$height)=getimagesize($uploadedfile);

            $newwidth1=200;
            $newheight1=112;
            $tmp1=imagecreatetruecolor($newwidth1,$newheight1);

            //imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
            $resourceatio = $width/$height;
            if($resourceatio > 16/9){
                $width = intval($height * 16/9);
            }
            else{
                $height = intval($width * 9/16);
            }

            imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
            
            if (!is_dir($resource->getUploadRootDir($resource->getType()).'thumbs/')) {
                mkdir($resource->getUploadRootDir($resource->getType()).'thumbs/', 0777);
            }
            $filename1 = $resource->getUploadRootDir($resource->getType()).'thumbs/'.$resource->getFilename().'.jpg';

            imagejpeg($tmp1,$filename1,100);

            imagedestroy($src);
            
            imagedestroy($tmp1);

        } elseif($resource->getType() == 'video'){
    
            if (!is_dir($resource->getUploadRootDir($resource->getType()).'thumbs/')) {
                mkdir($resource->getUploadRootDir($resource->getType()).'thumbs/', 0777);
            }

            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($resource->getUploadRootDir($resource->getType()).$resource->getFilename().'.'.$resource->getExtension());
            $video
                ->filters()
                ->resize(new \FFMpeg\Coordinate\Dimension(200, 112))
                ->synchronize();

            $snapTime = 10;
            if($resource->duration <= 10) {
                $snapTime = 0;                
            }

            $video
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($snapTime))
                ->save($resource->getUploadRootDir($resource->getType()).'thumbs/'.$resource->getFilename().'.jpg');
        } elseif($resource->getType() == 'audio'){
        // Takes resource->customAudioArt 
        // and moves it to audio/thumbs directory
        // TODO: Need to create resized view for Browsing
            $audioart_path = $resource->getUploadRootDir($resource->getType()).'thumbs/' ;
            if(null !== $resource->customAudioArt)
            {
                $ext = strtolower(pathinfo($resource->customAudioArt
                    ->getClientOriginalName(), PATHINFO_EXTENSION));
                if(!($ext == 'jpg' || $ext == 'jpeg')) 
                { // Accepted file formats
                    return;
                }
                if (!is_dir($resource->getUploadRootDir($resource->getType()).'thumbs/')) 
                { // Create art folder container if necessary
                    try{
                        mkdir($audioart_path);
                    } catch (IOException $e) {
                        return $e->getMessage();
                    }
                }
                try {
                // Move the randomly named file to a standard file location 
                   $resource->customAudioArt->move($audioart_path, $resource->getId().".".$ext);
                    unset($resource->customAudioArt);
                    $resource->setCustomArtValue(true);  
                } catch (FileException $e) {
                    return $e->getMessage();
                }
                
            }
        }
    }

    /**
     * storeFilenameForRemove
     */
    public function preRemove(Resource $resource, LifecycleEventArgs $args)
    {
    	$rm_file_named = $resource->getUploadRootDir($resource->getType()).$resource->getFilename().'.'.$resource->getExtension();
        if(null !== $rm_file_named){
            $resource->setFilenameForRemove($rm_file_named);    
        } else {
            throw new Exception("File not found while preparing file for remove: ".$rm_file_named); 
        }
    }

    /**
     * removeUpload
     */
    public function postRemove(Resource $resource, LifecycleEventArgs $args)
    {
        // Removing content file
    	$rm_file_named = $resource->getFilenameForRemove();
        if (null !== $rm_file_named) 
        {
			if(is_file($rm_file_named)) 
            {
				unlink($rm_file_named);
			} else {
                // Throwing the exception will block Doctrine
                // from detaching the dead (file-less) entity
                //throw new Exception("File not found while trying to unlink: ".$rm_file_named);
            }
            // Removing Thumb File
            if(!in_array($resource->getType(), 
                array('application', 'pdf', 'text')))
            {
                // Default thumb filename
                $thumb_f_name = $resource->getFilename(); 
                // An audio file may or may not have custom art.
                if($resource->getType() == 'audio' 
                    && !$resource->hasCustomArt()){
                    return; // Nothing to remove
                } else {
                    $thumb_f_name = $resource->getId();
                }
                // Resolve thumb absolute path and delete it if we can.
				$thumbsAbsolutePath = $resource->getUploadRootDir($resource->getType()).'thumbs/'.$thumb_f_name.'.jpg';
				if(is_file($thumbsAbsolutePath)) {
					unlink($thumbsAbsolutePath);
				} else {
                    // throw new Exception("Thumb image not found: ".$thumbsAbsolutePath);
                    
                }
            }
        }
    }
}
