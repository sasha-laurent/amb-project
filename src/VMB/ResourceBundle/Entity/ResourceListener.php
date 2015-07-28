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
        	// TODO: Filter/match authorized & unauthorized extensions? Binary code could be injected in images
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
                throw new Exception(" Unauthorized file extension.", 1);
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
        vous devez lancer une exception ici si le fichier ne peut pas
        être déplacé afin que l'entité ne soit pas persistée dans la
        base de données comme le fait la méthode move() de UploadedFile
        et on ajoute le reste a partir du type_mime et l'user
    */
    	try {
	    	// If directories do not exist yet, create them (default mode : 777)
	        if (!is_dir($resource->getUploadRootDir($resource->getType())))
	        {
	            if (!is_dir($resource->getUploadRootDir())) {
	            	try {
	                	mkdir($resource->getUploadRootDir());	
	            	} catch (IOException $e) {
	            		// Do something with it
	            	}
	            }
	            try {
	            	mkdir($resource->getUploadRootDir($resource->getType()));	
	            } catch (IOException $e) {
	            	// Do something with it
	            }
	        }
	        $resource->file->move($resource->getUploadRootDir($resource->getType()), $resource->getFilename().'.'.$resource->getExtension());
	        unset($resource->file);     	
    	} catch (FileException $e) {
    		$em = $args->getEntityManager();
    		$em->detach($resource);
    		return $e;
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
        }
    }

    /**
     * storeFilenameForRemove
     */
    public function preRemove(Resource $resource, LifecycleEventArgs $args)
    {
    	$rm_file_named = $resource->getUploadRootDir($resource->getType()).$resource->getFilename().'.'.$resource->getExtension();
        $resource->setFilenameForRemove($rm_file_named);
    }

    /**
     * removeUpload
     */
    public function postRemove(Resource $resource, LifecycleEventArgs $args)
    {
    	$rm_file_named = $resource->getFilenameForRemove();
        if (null !== $rm_file_named) {
			if(is_file($rm_file_named)) {
				unlink($rm_file_named);
			}
            if(!in_array($resource->getType(), array('application', 'text', 'audio'))){
				$thumbsAbsolutePath = $resource->getUploadRootDir($resource->getType()).'thumbs/'.$resource->getFilename().'.jpg';
				if(is_file($thumbsAbsolutePath)) {
					unlink($thumbsAbsolutePath);
				}
            }
        }
    }
}