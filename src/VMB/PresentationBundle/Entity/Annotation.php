<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Annotation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\AnnotationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Annotation
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
     * @ORM\Column(name="extension", type="string", length=10, nullable=true)
     */
    private $extension;

    /**
     * @var integer
     *
     * @ORM\Column(name="length", type="integer")
     */
    private $length = 12;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="fontSize", type="integer")
     */
    private $fontSize = 100;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fontColor", type="string")
     */
    private $fontColor = '#000000';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var float
     *
     * @ORM\Column(name="positionX", type="float")
     */
    private $positionX = 50;

    /**
     * @var float
     *
     * @ORM\Column(name="positionY", type="float")
     */
    private $positionY = 50;

    /**
     * @var integer
     *
     * @ORM\Column(name="beginning", type="integer")
     */
    private $beginning = 0;
    
    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Presentation", inversedBy="annotations")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE") 
	*/
	private $presentation;
	
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

    /**
     * Set type
     *
     * @param string $type
     * @return Annotation
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set length
     *
     * @param integer $length
     * @return Annotation
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Annotation
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
     * Set positionX
     *
     * @param float $positionX
     * @return Annotation
     */
    public function setPositionX($positionX)
    {
        $this->positionX = $positionX;

        return $this;
    }

    /**
     * Get positionX
     *
     * @return float 
     */
    public function getPositionX()
    {
        return $this->positionX;
    }

    /**
     * Set positionY
     *
     * @param float $positionY
     * @return Annotation
     */
    public function setPositionY($positionY)
    {
        $this->positionY = $positionY;

        return $this;
    }

    /**
     * Get positionY
     *
     * @return float 
     */
    public function getPositionY()
    {
        return $this->positionY;
    }

    /**
     * Set beginning
     *
     * @param integer $beginning
     * @return Annotation
     */
    public function setBeginning($beginning)
    {
        $this->beginning = $beginning;

        return $this;
    }

    /**
     * Get beginning
     *
     * @return integer 
     */
    public function getBeginning()
    {
        return $this->beginning;
    }

    /**
     * Set presentation
     *
     * @param \VMB\PresentationBundle\Entity\Presentation $presentation
     * @return Annotation
     */
    public function setPresentation(\VMB\PresentationBundle\Entity\Presentation $presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return \VMB\PresentationBundle\Entity\Presentation 
     */
    public function getPresentation()
    {
        return $this->presentation;
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
        if(in_array($extension, array('mp3', 'ogg'))) {
			$path = str_replace('\\', '/', __DIR__).'/../../../../web/upload/annotations/';
			
			$this->file->move($path, $this->getId().'.'.$this->getExtension());
		}
    }
    
    public function getFilePath()
    {
		if(is_file(str_replace('\\', '/', __DIR__).'/../../../../web/upload/annotations/'.$this->getId().'.'.$this->getExtension())) {
			return 'upload/annotations/'.$this->getId().'.'.$this->getExtension();
		}
		else return '';
	}
	
	/**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = str_replace('\\', '/', __DIR__).'/../../../../web/upload/annotations/'.$this->getId().'.'.$this->getExtension();
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
     * Set fontSize
     *
     * @param integer $fontSize
     * @return Annotation
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * Get fontSize
     *
     * @return integer 
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * Set fontColor
     *
     * @param string $fontColor
     * @return Annotation
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;

        return $this;
    }

    /**
     * Get fontColor
     *
     * @return string 
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }
}
