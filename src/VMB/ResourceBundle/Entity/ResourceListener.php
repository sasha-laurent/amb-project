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
        	// Supported File Formats
            $exts_whitelist = array(
                'ogg' => 'audio', 'mp3'  => 'audio',
                'jpeg' => 'image', 'jpg' => 'image', 'png' => 'image',
                'mp4' => 'video', 'webm' => 'video', 'ogg' => 'video',
                'pdf' => 'pdf');
            $mime_type_whitelist = array();

            $extension = strtolower(pathinfo($resource->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $resource->setExtension($extension);
            $resource->mime_type = $resource->file->getMimeType();
            $mime_arr = explode("/", $resource->mime_type);
            $primary_typ = $mime_arr[0];
            $secondary_typ = $mime_arr[1];
            
            /*
             * Taking some elementary precautions.
             * Notes:
             * - Null is a valid File::getMimeType() result
             *
            if (null === $resource->mime_type){
            	$em->detach($resource);
            	throw new Exception(" Unrecognized media type.", 1);
            }
            */
            
            if(!in_array($extension, 
                array_keys($exts_whitelist)))
            {
                throw new Exception("Unauthorized file extension.", 1);
            } else {
                if($primary_typ == 'application' 
                    && $secondary_typ != 'pdf'){
                    // Try to set type by guessing extension
                    // Either extension & mime type are valid or we detach the entity
                    // MP3s are sometimes parsed as "application/octet-stream"
                    $guessed_ext = $resource->file->guessExtension();
                    if(array_key_exists($guessed_ext, $exts_whitelist)){
                        $resource->setType($exts_whitelist[$guessed_ext]);
                    } else {
                        throw new Exception("Unauthorized resolved file type.", 1);
                        
                    }
                } elseif($primary_typ == 'application' 
                    && $secondary_typ == 'pdf'){
                    $resource->setType($secondary_typ);
                } else {
                    $resource->setType($primary_typ);
                }
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
        }
    }
    
    /**
     * upload
     */
    public function postPersist(Resource $resource, LifecycleEventArgs $args)
    {   
        if (null === $resource->file 
            && null === $resource->customAudioArt){
            return;
        }

        /*    
        Lancer une exception ici si le fichier ne peut pas
        être déplacé afin que l'entité ne soit pas persistée dans la base de données
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
	            		return $e;
	            	}
	            }
	            try {
	            	mkdir($resource->getUploadRootDir($resource->getType()));	
	            } catch (IOException $e) {
	            	return $e;
	            }
	        }
	        $resource->file->move($resource->getUploadRootDir($resource->getType()), 
                $resource->getFilename().'.'.$resource->getExtension());
	        unset($resource->file);     	
    	} catch (FileException $e) {
    		return $e;
    	}
        // Le fichier a été déplacé, on peut créer les miniatures.
        $extension = $resource->getExtension();
        if(in_array($extension, 
            array('jpeg', 'jpg', 'png'))){
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
            if($resource->getDuration() <= 10) {
                $snapTime = 0;                
            }

            $video
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($snapTime))
                ->save($resource->getUploadRootDir($resource->getType()).'thumbs/'.$resource->getFilename().'.jpg');

        } elseif($resource->getType() == 'audio' 
            && $resource->hasCustomArt()){
            /*
             * TODO: Make thumb for Resource/browse out of customArt 
            **/
        } elseif ($resource->getType() == 'pdf')
        {
            /*
            ** TODO : Imagick first PDF page and transform it into a JPEG
            **
            */

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
                // Throwing the exception blocks Doctrine
                // from detaching the dead (file-less) entity
                // return new Exception("File not found while trying to unlink: ".$rm_file_named);
            }
        }
        // Resolve thumb absolute path if it exists
        // and delete it if we can.
        if($resource->getType() == 'audio' 
            && $resource->hasCustomArt())
        {
            $thumbsAbsolutePath = $resource->getCustomArtPath(true);
            if(is_file($thumbsAbsolutePath)) 
            {
                unlink($thumbsAbsolutePath);
            } else {
                // return new Exception("Thumb image not found: ".$thumbsAbsolutePath);  
            }    
        }
    }
}
