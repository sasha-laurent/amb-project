<?php
// src\MB\ResourceBundle\Entity\ResourceListener;
namespace VMB\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VMB\ResourceBundle\Entity\Resource;
use \GetId3\GetId3Core as GetId3;

class ResourceListener
{
    /**
     * @ORM\PrePersist()
     */
    public function preUpload(Resource $r, LifecycleEventArgs $args)
    {
        if (null !== $this->file)
        {
            $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $this->setExtension($extension);
            $this->mime_type = explode("/",$this->file->getMimeType());
            $primary_typ = $this->mime_type[0];
            $secondary_typ = $this->mime_type[1];
            // Big Danger
            if($primary_type == 'application' && $secondary_typ != 'pdf' || null === $this->mime_type){
                return;
            }
            $this->setType();
            $this->setSize(filesize($this->file));
            $this->setPath("");
           
           /*Analyse du fichier avec getID3 */
           $getId3 = new GetId3();
           $analyse = $getId3
                ->setOptionMD5Data(true)
                ->setOptionMD5DataSource(true)
                ->setEncoding('UTF-8')
                ->analyze($this->file);
               
            if(in_array($extension, array('jpeg', 'jpg', 'png'))){
                
                $this->setHeight($analyse['video']['resolution_y']);
                $this->setWidth($analyse['video']['resolution_x']);
            }
            elseif(in_array($extension, array('ogg', 'mp3'))){
                $this->setDuration($analyse['playtime_seconds']);
            }
            elseif($this->getType() == 'video'){
                $this->setDuration($analyse['playtime_seconds']);   
            } 
        }
    }
    
    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (null === $this->file){
            return;
        }
        $extension = $this->getExtension();
        // vous devez lancer une exception ici si le fichier ne peut pas
        // être déplacé afin que l'entité ne soit pas persistée dans la
        // base de données comme le fait la méthode move() de UploadedFile
        // et on ajoute le reste a partir du type_mime et l'user
    
        if (!is_dir($this->getUploadRootDir($this->getType())))
        {
            if (!is_dir($this->getUploadRootDir())) {
                mkdir($this->getUploadRootDir(), 0777);
            }
            mkdir($this->getUploadRootDir($this->getType()), 0777);
        }
        $this->file->move($this->getUploadRootDir($this->getType()), $this->getFilename().'.'.$this->getExtension());
        unset($this->file); 

        if(in_array($this->getExtension(), array('jpeg', 'jpg', 'png'))){
            // on crée la miniature
            // http://php.net/manual/en/function.imagecreate.php
            if($extension =="jpg" || $extension =="jpeg" ){

                $uploadedfile = $this->getUploadRootDir($this->getType()).$this->getFilename().'.'.$this->getExtension();
                $src = imagecreatefromjpeg($uploadedfile);
            }
            else if($extension=="png"){
                $uploadedfile = $this->getUploadRootDir($this->getType()).$this->getFilename().'.'.$this->getExtension();
                $src = imagecreatefrompng($uploadedfile);
            }
 
            list($width,$height)=getimagesize($uploadedfile);

            $newwidth1=200;
            $newheight1=112;
            $tmp1=imagecreatetruecolor($newwidth1,$newheight1);

            //imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
            $ratio = $width/$height;
            if($ratio > 16/9){
                $width = intval($height * 16/9);
            }
            else{
                $height = intval($width * 9/16);
            }

            imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
            
            if (!is_dir($this->getUploadRootDir($this->getType()).'thumbs/')) {
                mkdir($this->getUploadRootDir($this->getType()).'thumbs/', 0777);
            }
            $filename1 = $this->getUploadRootDir($this->getType()).'thumbs/'.$this->getFilename().'.jpg';

            imagejpeg($tmp1,$filename1,100);

            imagedestroy($src);
            
            imagedestroy($tmp1);
        }

        elseif($this->getType() == 'video'){
    
            if (!is_dir($this->getUploadRootDir($this->getType()).'thumbs/')) {
                mkdir($this->getUploadRootDir($this->getType()).'thumbs/', 0777);
            }

            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($this->getUploadRootDir($this->getType()).$this->getFilename().'.'.$this->getExtension());
            $video
                ->filters()
                ->resize(new \FFMpeg\Coordinate\Dimension(200, 112))
                ->synchronize();

            $snapTime = 10;
            if($this->duration <= 10) {
                $snapTime = 0;                
            }

            $video
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($snapTime))
                ->save($this->getUploadRootDir($this->getType()).'thumbs/'.$this->getFilename().'.jpg');
        }
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getUploadRootDir($this->getType()).$this->getFilename().'.'.$this->getExtension();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->filenameForRemove) {
			if(is_file($this->filenameForRemove)) {
				unlink($this->filenameForRemove);
			}
            if(!in_array($this->getType(), array('application', 'text', 'audio'))){
				$thumbsAbsolutePath = $this->getUploadRootDir($this->getType()).'thumbs/'.$this->filename.'.jpg';
				if(is_file($thumbsAbsolutePath)) {
					unlink($thumbsAbsolutePath);
				}
            }
        }
    }
}
