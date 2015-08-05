<?php

namespace VMB\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Resource
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\ResourceBundle\Entity\ResourceRepository")
 * @ORM\EntityListeners({"ResourceListener"}) 
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
     * @ORM\Column(name="keywords", type="text")
     */
    private $keywords;

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
    * @ORM\ManyToMany(targetEntity="VMB\PresentationBundle\Entity\Topic")
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

    /**
     * @Assert\File(maxSize="128000000000")
     */
    public $customAudioArt;

    /**
     * @ORM\Column(name="customArt", type="string", length=128, nullable=true)
     * @var string 
     * Use it to memorize whether an Audio file has custom album art
     * attached to it or not.
     * The path is relative to the web/ folder, and not absolute to  
     * the root folder (as * such direct is_file(customArtPath) 
     * will always fail).
     */
    private $customArtPath = null; 

    /*
    * Variable de mime
    */
    public $mime_type;

    /**
     * @var string
     * Absolute path to file.
     *
    **/
    private $filenameForRemove = null; 

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topic = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Get type as a title
     *
     * @return string 
     */
    public function getTypeTitle()
    {
        if($this->type == 'audio') {
			return 'Fichiers audio';
		}
		elseif($this->type == 'video') {
			return 'Vidéos';
		}
		elseif($this->type == 'image') {
			return 'Images';
		}
		elseif($this->type == 'text') {
			return 'Textes';
		}
		else {
			return 'Autres fichiers';
		}
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

    public function getUploadRootDir($mime_type = null)
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return str_replace('\\', '/', __DIR__).'/../../../../web/'.$this->getUploadDir($mime_type);
    }

    public function getUploadDir($mime_type = null)
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

    public function getFilenameForRemove()
    {
        return $this->filenameForRemove;
    }

    public function setFilenameForRemove($str = null)
    {
        $this->filenameForRemove = $str;
    }
    
    public function getResourcePath() {
		return $this->getUploadDir($this->getType()).$this->filename.'.'.$this->extension;
	}

    /**
     * @param noparam
     * @return boolean : Audio resource has custom art or not
    **/
    public function hasCustomArt(){
        if(isset($this->customAudioArt))
        {
            return true;
        }
        elseif(null !== $this->customArtPath) 
        {
            return true;
        } else {
            // Try to resolve based on resource Id and different accepted file extensions.
            $base_path = $this->getUploadRootDir('audio').'thumbs/'.$this->id;
            foreach (array('jpeg', 'jpg', 'png') as $ext) {
                $file_resolve = $base_path.".".$ext;
                if(is_file($file_resolve))
                {
                    $this->customArtPath = $this->getUploadDir('audio').'thumbs/'.$this->id.'.'.$ext;
                    return true;
                }
            }
            return false;
        }
    }
    /**
     * @param Whether the path to file to return should be absolute or not.
     * @return Relative|Absolute path to custom art file on server.
     * @return null value if the custom (relative) path is found not 
     * to be set. 
     * Most of the time the hasCustomArt function will be called
     * first. This mean the file resolution will be run once already, 
     * as such there is no need to run it again. 
    */
    public function getCustomArtPath($absolute_path = false){
        if(isset($this->customArtPath) && !$absolute_path)
        {
            return $this->customArtPath;
        } else if (isset($this->customArtPath) && $absolute_path) {
            $abs_p = str_replace('\\', '/', __DIR__).'/../../../../web/'.$this->customArtPath;
            if(is_file($abs_p))
            {
                return $abs_p;
            } else {
                return null;
            }
        } else {
            /*
            // Try to resolve based on resource Id and different accepted file extensions.
            $base_path = $this->getUploadRootDir('audio').'thumbs/'.$this->id;
            foreach (array('jpeg', 'jpg', 'png') as $ext) {
                $file_resolve = $base_path.".".$ext;
                if(is_file($file_resolve))
                {
                    $this->customArtPath = $this->getUploadDir('audio').'thumbs/'.$this->id.'.'.$ext;
                    return $this->customArtPath;
                }
            }*/
            return null;
        }
    }
    /**
     * @param Relative path to /var/www/edu/web/ directory
     * @return void
    **/
    public function setCustomArtPath($path){
        $this->customArtPath = $path;
    }

    public function persistCustomAudioArt()
    {
        /*
         * Takes resource->customAudioArt 
         * and moves it to audio/thumbs directory
         */
        $ul_root_audio_path = $this->getUploadRootDir('audio')
        .'thumbs/';
        $ext = strtolower(pathinfo($this->customAudioArt->getClientOriginalName(), PATHINFO_EXTENSION));
        // Accepted file formats
        if(!in_array($ext, array('jpeg', 'jpg', 'png'))) 
        {
            throw new Exception("Invalid thumb file extension");
        }
        // Create art folder container if necessary
        if (!is_dir($ul_root_audio_path)) 
        { 
            try{
                mkdir($ul_root_audio_path);
            } catch (IOException $e) {
                return $e;
            }
        }
        // Move the randomly named file to a standard file location 
        try {
           $artPath = $this->getId().".".$ext;
           $this->customAudioArt->move($ul_root_audio_path, $artPath);
           $this->setCustomArtPath($resource->getUploadDir('audio').'thumbs/'.$artPath); 
           unset($this->customAudioArt);
       } catch (FileException $e) {
        return $e;
        }
    }

    /**
     * Tries at the best of its ability to remove any trace
     * of custom art on server and as object attribute.
     * @return void
    **/
    public function removeCustomArt(){
        $customArtAbsolutePath = $this->getCustomArtPath(true);
        if(is_file($customArtAbsolutePath))
        {
            unlink($customArtAbsolutePath);
        } else {
            // throw new Exception("File Not Found: ".$customArtAbsolutePath, 1); 
        }
        if(isset($this->customArtPath))
        {
            unset($this->customArtPath);
        }
    }

    public function getThumbsPath()
    {
        if($this->hasCustomArt()){
            return $this->getCustomArtPath();            
        } else if(in_array($this->getType(), 
            array('application', 'pdf', 'text', 'audio'))) {
            return 'img/icon/'.$this->getType().'.jpg';
        } else {
            return $this->getUploadDir(
                $this->getType()).'thumbs/'.$this->filename.'.jpg';   
        }
    }
    
    public function getGlyphicon()
    {
		$assoc = array('video' => 'film', 'image' => 'picture', 'audio' => 'volume-up', 'application' => 'paperclip', 'pdf' => 'file', 'text' => 'file');
        if(array_key_exists($this->type, $assoc)){
            return $assoc[$this->type];
        } else {
            return 'question-sign';
        } 
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
     * Is the given User the author of this Post?
     *
     * @return bool
     */
    public function isOwner(\VMB\UserBundle\Entity\User $user = null)
    {
        return $user && $this->getOwner() && $user->getId() == $this->getOwner()->getId();
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

    /**
     * Add topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     * @return Resource
     */
    public function addTopic(\VMB\PresentationBundle\Entity\Topic $topic)
    {
        $this->topic[] = $topic;

        return $this;
    }

    /**
     * Remove topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     */
    public function removeTopic(\VMB\PresentationBundle\Entity\Topic $topic)
    {
        $this->topic->removeElement($topic);
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Resource
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
