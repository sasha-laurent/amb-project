<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Topic
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\TopicRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Topic
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
     * @Gedmo\Slug(fields={"title"},updatable=false)
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     * */
    private $slug;
    
    /**
     * @Assert\File(maxSize="128000000000")
     * 
     */
    public $file;

	private $filenameForRemove;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function toString() { return $this->getTitle(); }

    /**
     * Set title
     *
     * @param string $title
     * @return Topic
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
     * Set slug
     *
     * @param string $slug
     * @return Resource
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function preUpload()
    {
        
    }

    public function upload()
    {
        if (null === $this->file){
            return;
        }
        $extension = strtolower(pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION));
        if(in_array($extension, array('jpg', 'jpeg'))) {
			$path = str_replace('\\', '/', __DIR__).'/../../../../web/upload/topic/';
			dump($path);
			$this->file->move($path, $this->getSlug().'.jpg');
			
			$uploadedfile = $path.$this->getSlug().'.jpg';
			dump($uploadedfile);
			$src = imagecreatefromjpeg($uploadedfile);
			
			list($width,$height) = getimagesize($uploadedfile);
			$newWidth=205;
			$newHeight=155;
			$tmp = imagecreatetruecolor($newWidth,$newHeight);
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		
			imagejpeg($tmp, $uploadedfile);

			imagedestroy($src);
			imagedestroy($tmp);
			
			unset($this->file);
		}
    }
    
    public function getThumbsPath()
    {
		$path = 'upload/topic/';
		return $path.$this->getSlug().'.jpg';
	}
	    
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = str_replace('\\', '/', __DIR__).'/../../../../web/upload/topic/'.$this->getSlug().'.jpg';
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
}

