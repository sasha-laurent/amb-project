<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Topic
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table()
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\TopicRepository")
 * @ORM\EntityListeners({"TopicListener"}) 
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
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Topic", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
    
    /**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\Ontology", mappedBy="topic", cascade={"remove", "persist", "detach"})
	*/
	private $ontologies;
    
    /**
     * @Assert\File(maxSize="128000000000")
     * 
     */
    public $file;

	private $filenameForRemove;

    /**
    ** @var integer
    ** Total included presentations (own+children's) count column
    ** @ORM\Column(name="total_included_presentations", type="integer")
    **
    */
    public $total_included_presentations = 0;

	/**
	 * Used for select form components
	 * 
	 */
	public function __toString() {
		$output = '';
		for($i=0; $i < $this->lvl; $i++) {
			$output .= '—';
		}
		return $output.$this->title;
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
			
			$this->file->move($path, $this->getSlug().'.jpg');
			
			$uploadedfile = $path.$this->getSlug().'.jpg';
			
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
		if(is_file(str_replace('\\', '/', __DIR__).'/../../../../web/upload/topic/'.$this->getSlug().'.jpg')) {
			return 'upload/topic/'.$this->getSlug().'.jpg';
		}
		else return 'img/icon/default_topic_thumb.jpg';
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return Topic
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return Topic
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return Topic
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return Topic
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \VMB\PresentationBundle\Entity\Topic $parent
     * @return Topic
     */
    public function setParent(\VMB\PresentationBundle\Entity\Topic $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \VMB\PresentationBundle\Entity\Topic 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \VMB\PresentationBundle\Entity\Topic $children
     * @return Topic
     */
    public function addChild(\VMB\PresentationBundle\Entity\Topic $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \VMB\PresentationBundle\Entity\Topic $children
     */
    public function removeChild(\VMB\PresentationBundle\Entity\Topic $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add ontologies
     *
     * @param \VMB\PresentationBundle\Entity\Ontology $ontologies
     * @return Topic
     */
    public function addOntology(\VMB\PresentationBundle\Entity\Ontology $ontologies)
    {
        $this->ontologies[] = $ontologies;

        return $this;
    }

    /**
     * Remove ontologies
     *
     * @param \VMB\PresentationBundle\Entity\Ontology $ontologies
     */
    public function removeOntology(\VMB\PresentationBundle\Entity\Ontology $ontologies)
    {
        $this->ontologies->removeElement($ontologies);
    }

    /**
     * Get ontologies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOntologies()
    {
        return $this->ontologies;
    }

    /**
     * Get Total included Presentations Count
     * @return integer
     */
    public function getTotalIncludedPresentations(){
        return $this->total_included_presentations;
    }

    /**
     * Set new Total for included Presentations
     * @param integer
     */
    public function setTotalIncludedPresentations($i){
        $this->total_included_presentations = $i;
    }
}
