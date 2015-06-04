<?php

namespace VMB\ContextualHelpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Help
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\ContextualHelpBundle\Entity\HelpRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Help
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasVideo", type="boolean", options={"default":0})
     */
    private $hasVideo = 0;
    
    /**
     * @var array of strings
     *
     * @ORM\Column(name="route", type="simple_array")
     */
    private $route;
    
    /**
     * @Assert\File(maxSize="128000000000")
     */
    public $file;
    
    private $filenameForRemove = null;
    
    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=6)
     */
    private $extension;


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
     * Set text
     *
     * @param string $text
     * @return Help
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set video
     *
     * @param string $video
     * @return Help
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set hasVideo
     *
     * @param boolean $hasVideo
     * @return Help
     */
    public function setHasVideo($hasVideo)
    {
        $this->hasVideo = $hasVideo;

        return $this;
    }

    /**
     * Get hasVideo
     *
     * @return boolean 
     */
    public function getHasVideo()
    {
        return $this->hasVideo;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Help
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }
    
    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
        if (null !== $this->file)
        {
            $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
            $this->extension = $extension;
            $mimeType = explode("/",$this->file->getMimeType());
            $type = $mimeType[0];
            
            if($type != 'video') {
				unset($this->file);
			}
			else {
				$this->hasVideo = true;
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
        $this->file->move(str_replace('\\', '/', __DIR__).'/../../../../web/upload/help/', $this->getId().'.'.$this->extension);
        
        unset($this->file); 
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
		if($this->hasVideo) {
			$this->filenameForRemove = str_replace('\\', '/', __DIR__).'/../../../../web/.'.$this->getVideoPath();
		}
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
        }
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Help
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
    
    public function getVideoPath() {
		return 'upload/help/'.$this->getId().'.'.$this->extension;
	}

    /**
     * Set extension
     *
     * @param string $extension
     * @return Help
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
}
