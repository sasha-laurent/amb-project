<?php

namespace VMB\PresentationBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Presentation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\PresentationRepository")
 */
class Presentation
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
     * @ORM\Column(name="title", type="string", length=64)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean", options={"default":0})
     */
    private $public = 0;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="official", type="boolean", options={"default":0})
     */
    private $official = 0;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

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
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="updateMessage", type="text", nullable=true)
     */
    private $updateMessage;
    
    /**
	* @ORM\ManyToOne(targetEntity="VMB\UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	*/
	private $owner;
    
    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Matrix")
	* @ORM\JoinColumn(nullable=false) 
	*/
	private $matrix;
	
	/**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Topic")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	private $topic;
	
	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\CheckedResource", mappedBy="presentation", cascade={"remove", "persist", "detach"})
	*/
	private $resources;
	
	private $sortedResources = null;


	/**
     * Constructor
     */
    public function __construct($matrix)
    {
        $this->setMatrix($matrix);
        $this->setTopic($matrix->getTopic());
    }
    
    
    /**
     * Is the given User the author of this Post?
     *
     * @return bool
     */
    public function isOwner(\VMB\UserBundle\Entity\User $user = null)
    {
        return $user && $user->getId() == $this->getOwner()->getId();
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
     * @return Presentation
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
     * @return Presentation
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Presentation
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
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return Presentation
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
     * Set duration
     *
     * @param integer $duration
     * @return Presentation
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
     * Set updateMessage
     *
     * @param string $updateMessage
     * @return Presentation
     */
    public function setUpdateMessage($updateMessage)
    {
        $this->updateMessage = $updateMessage;

        return $this;
    }

    /**
     * Get updateMessage
     *
     * @return string 
     */
    public function getUpdateMessage()
    {
        return $this->updateMessage;
    }

    /**
     * Set owner
     *
     * @param \VMB\UserBundle\Entity\User $owner
     * @return Presentation
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
     * Set matrix
     *
     * @param \VMB\PresentationBundle\Entity\Matrix $matrix
     * @return Presentation
     */
    public function setMatrix(\VMB\PresentationBundle\Entity\Matrix $matrix = null)
    {
        $this->matrix = $matrix;

        return $this;
    }

    /**
     * Get matrix
     *
     * @return \VMB\PresentationBundle\Entity\Matrix 
     */
    public function getMatrix()
    {
        return $this->matrix;
    }
    
    public function toString()
    {
		return $this->title;
	}

    /**
     * Add resources
     *
     * @param \VMB\PresentationBundle\Entity\CheckedResource $resources
     * @return Presentation
     */
    public function addResource(\VMB\PresentationBundle\Entity\CheckedResource $resources)
    {
        $this->resources[] = $resources;

        return $this;
    }

    /**
     * Remove resources
     *
     * @param \VMB\PresentationBundle\Entity\CheckedResource $resources
     */
    public function removeResource(\VMB\PresentationBundle\Entity\CheckedResource $resources)
    {
        $this->resources->removeElement($resources);
    }
    
    public function clearResources()
    {
		$this->resources->clear();
	}

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResources()
    {
        return $this->resources;
    }
    
    public function isChecked($usedResourceId)
    {
		if(!is_array($this->sortedResources)) {
			$this->sortResources();
		}
        if(isset($this->sortedResources[$usedResourceId])) {
			return !($this->sortedResources[$usedResourceId]->getSuggested());
		}
		else { return false; }
    }
    
    public function isSuggested($usedResourceId)
    {
		if(!is_array($this->sortedResources)) {
			$this->sortResources();
		}
        if(isset($this->sortedResources[$usedResourceId])) {
			return $this->sortedResources[$usedResourceId]->getSuggested();
		}
		else { return false; }
    }
    
    private function sortResources()
    {
		$this->sortedResources = array();
		if($this->resources != null) {
			foreach($this->resources as $resource) {
				$this->sortedResources[$resource->getUsedResource()->getId()] = $resource;
			}
		}
	}

    /**
     * Set topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     * @return Presentation
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
     * Set public
     *
     * @param boolean $public
     * @return Presentation
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set official
     *
     * @param boolean $official
     * @return Presentation
     */
    public function setOfficial($official)
    {
        $this->official = $official;

        return $this;
    }

    /**
     * Get official
     *
     * @return boolean 
     */
    public function getOfficial()
    {
        return $this->official;
    }
}
