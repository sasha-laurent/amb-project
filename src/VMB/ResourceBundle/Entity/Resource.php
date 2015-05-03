<?php

namespace VMB\ResourceBundle\Entity;
//include('SimpleImage.php');

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use \GetId3\GetId3Core as GetId3;
/**
 * Resource
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\ResourceBundle\Entity\ResourceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Resource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
    * @ORM\ManyToOne(targetEntity="VMB\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=true, onDelete="SET NULL") 
    */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text")
     */
    private $path;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"},updatable=false)
     * @ORM\Column(name="filename", type="string", length=128, unique=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=16)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=5)
     */
    private $extension;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dateCreate", type="date")
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="dateUpdate", type="date")
     */
    private $dateUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="encodage", type="string", length=255, nullable=true)
     */
    private $encodage;

    /**
    * @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Topic")
    * @ORM\JoinColumn(nullable=false) 
    */
    private $topic;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="trusted", options={"default":0})
     */
    private $trusted = 0;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="indexed", options={"default":0})
     */
    private $indexed = 0;
    
    /**
     * @Assert\File(maxSize="128000000000")
     */
    public $file;

    /*
    * Variable de mime
    */
    public $mime_type;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Resource
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Resource
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Resource
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Resource
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Resource
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Resource
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Resource
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateUpload
     *
     * @param \DateTime $dateUpload
     * @return Resource
     */
    public function setDateUpload($dateUpload)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * Get dateUpload
     *
     * @return \DateTime 
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Resource
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }
    
    public function sizeToString()
    {
		$format = array('', 'K', 'M', 'G', 'T');
		$div = 1;
		foreach($format as $suffix) {
			$size = $this->size / $div;
			if($size < 1000) {
				return sprintf("%1\$.2f", $size).$suffix.'o';
			}
			$div *= 1000;
		}
		return sprintf("%1\$.2f", $size).' To';
	}

    /**
     * Set width
     *
     * @param integer $width
     * @return Resource
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Resource
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Resource
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }
    
    public function durationToString()
    {
		return sprintf("%02d", floor($this->duration / 60)).':'.sprintf("%02d", floor($this->duration % 60));
	}

    /**
     * Set encodage
     *
     * @param string $encodage
     * @return Resource
     */
    public function setEncodage($encodage)
    {
        $this->encodage = $encodage;

        return $this;
    }

    /**
     * Get encodage
     *
     * @return string 
     */
    public function getEncodage()
    {
        return $this->encodage;
    }
    
    public function getExt()
    {
        if (null != $this->file)
        {
        $this->ext = $this->file->guessExtension();
        return $this->ext;
        }
    }
    


    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
        if (null !== $this->file)
        {
            $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $this->setExtension($extension);
            $this->mime_type = explode("/",$this->file->getMimeType());
            $this->setType($this->mime_type[0]);
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

    protected function getUploadRootDir($mime_type = null)
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return str_replace('\\', '/', __DIR__).'/../../../../web/'.$this->getUploadDir($mime_type);
    }

    protected function getUploadDir($mime_type = null)
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        if($mime_type != null) {
            return 'upload/resources/'.$this->getOwner().'/'.$mime_type.'/';
        }
        else {
            return 'upload/resources/'.$this->getOwner().'/';   
        }
    }
    
    public function getResourcePath() {
		return $this->getUploadDir($this->getType()).$this->filename.'.'.$this->extension;
	}

    public function getThumbsPath()
    {
        if(in_array($this->getType(), array('application', 'text', 'audio'))) {
            return 'img/icon/'.$this->getType().'.jpg';
        }
        return $this->getUploadDir($this->getType()).'thumbs/'.$this->filename.'.jpg';
    }
    
    public function getGlyphicon()
    {
		$assoc = array('video' => 'film', 'image' => 'picture', 'audio' => 'volume-up', 'application' => 'paperclip', 'text' => 'file');
		return $assoc[$this->type];
	}


    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Resource
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return Resource
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set owner
     *
     * @param \VMB\UserBundle\Entity\User $owner
     * @return Resource
     */
    public function setOwner(\VMB\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \VMB\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     * @return Resource
     */
    public function setTopic(\VMB\PresentationBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \VMB\PresentationBundle\Entity\Topic 
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set trusted
     *
     * @param string $trusted
     * @return Resource
     */
    public function setTrusted($trusted)
    {
        $this->trusted = $trusted;

        return $this;
    }

    /**
     * Get trusted
     *
     * @return string 
     */
    public function getTrusted()
    {
        return $this->trusted;
    }

    /**
     * Set indexed
     *
     * @param string $indexed
     * @return Resource
     */
    public function setIndexed($indexed)
    {
        $this->indexed = $indexed;

        return $this;
    }

    /**
     * Get indexed
     *
     * @return string 
     */
    public function getIndexed()
    {
        return $this->indexed;
    }
}
