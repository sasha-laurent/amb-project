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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="filename", type="string", length=128, unique=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
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
     * @Assert\File(maxSize="60000000")
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
            //$this->setExtension($this->file->guessExtension());
            //$name = preg_replace('/[^a-zA-Z0-9]/', '-', $this->getTitle());
            //$this->filename = $name.sha1(uniqid(mt_rand(), false));
            $this->setExtension($this->file->guessExtension());
            $this->mime_type = explode("/",$this->file->getMimeType());
            $this->setType($this->mime_type[0]);
            $this->setSize(filesize($this->file));
            $this->setPath("");

           // $this->path = $this->filename.".".$this->getExtension();
        }
    }
    
    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // vous devez lancer une exception ici si le fichier ne peut pas
        // être déplacé afin que l'entité ne soit pas persistée dans la
        // base de données comme le fait la méthode move() de UploadedFile
        // et on ajoute le reste a partir du type_mime et l'user
        
           
        
         //$this->
        //$name =  $this->getTitle();
        //$this->filename = $name;
        /* je recupère le type grâce au mime*/
        
        if (!is_dir($this->getUploadRootDir($this->getType())))
        {
            if (!is_dir($this->getUploadRootDir())) {
                mkdir($this->getUploadRootDir(), 0777);
            }
            mkdir($this->getUploadRootDir($this->getType()), 0777);
        }
        $this->file->move($this->getUploadRootDir($this->getType()), $this->getFilename().'.'.$this->getExtension());
        unset($this->file); 
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->filenameForRemove) {
            unlink($this->filenameForRemove);
        }
    }

    protected function getUploadRootDir($mime_type = null)
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return str_replace('\\', '/', __DIR__).'/../../../../'.$this->getUploadDir($mime_type);
    }

    protected function getUploadDir($mime_type = null)
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        if($mime_type != null) {
            return 'web/upload/resources/'.$this->getOwner().'/'.$mime_type.'/';
        }
        else {
            return 'web/upload/resources/'.$this->getOwner().'/';   
        }
    }

    protected function getThumbsPath($filename)
    {
        return '/thumbs/'.$filename;
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
}
