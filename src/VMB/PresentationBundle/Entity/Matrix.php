<?php

namespace VMB\PresentationBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Matrix
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\MatrixRepository")
 */
class Matrix
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
	* @ORM\ManyToOne(targetEntity="VMB\UserBundle\Entity\User")
	*/
	private $owner;
	
	/**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Topic")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $topic;

	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\Pov", mappedBy="matrix", cascade={"remove", "persist"})
	*/
	private $povs;
	
	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\Level", mappedBy="matrix", cascade={"remove", "persist"})
	*/
	private $levels;
	
	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\UsedResource", mappedBy="matrix", cascade={"remove"})
	*/
	private $resources;
	
	private $sortedResources = null;

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
     * @return Matrix
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
     * @return Matrix
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
     * @return Matrix
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
     * @return Matrix
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
    
    public function toString()
    {
		return $this->title;
	}
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->povs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->levels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add povs
     *
     * @param \VMB\PresentationBundle\Entity\Pov $povs
     * @return Matrix
     */
    public function addPov(\VMB\PresentationBundle\Entity\Pov $povs)
    {
		$povs->setMatrix($this);
        $this->povs[] = $povs;

        return $this;
    }

    /**
     * Remove povs
     *
     * @param \VMB\PresentationBundle\Entity\Pov $povs
     */
    public function removePov(\VMB\PresentationBundle\Entity\Pov $povs)
    {
        $this->povs->removeElement($povs);
    }

    /**
     * Get povs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPovs()
    {
        return $this->povs;
    }

    /**
     * Add levels
     *
     * @param \VMB\PresentationBundle\Entity\Level $levels
     * @return Matrix
     */
    public function addLevel(\VMB\PresentationBundle\Entity\Level $levels)
    {
		$levels->setMatrix($this);
        $this->levels[] = $levels;

        return $this;
    }

    /**
     * Remove levels
     *
     * @param \VMB\PresentationBundle\Entity\Level $levels
     */
    public function removeLevel(\VMB\PresentationBundle\Entity\Level $levels)
    {
        $this->levels->removeElement($levels);
    }

    /**
     * Get levels
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set owner
     *
     * @param \VMB\UserBundle\Entity\User $owner
     * @return Matrix
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
    
    public function getDimension()
    {
		return $this->povs->count().'x'.$this->levels->count();
	}

    /**
     * Add resources
     *
     * @param \VMB\PresentationBundle\Entity\UsedResource $resources
     * @return Matrix
     */
    public function addResource(\VMB\PresentationBundle\Entity\UsedResource $resources)
    {
        $this->resources[] = $resources;

        return $this;
    }

    /**
     * Remove resources
     *
     * @param \VMB\PresentationBundle\Entity\UsedResource $resources
     */
    public function removeResource(\VMB\PresentationBundle\Entity\UsedResource $resources)
    {
        $this->resources->removeElement($resources);
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
    
    public function getUsedResourceById($id)
    {
		foreach($this->resources as $usedRes) {
			if($usedRes->getId() == $id) {
				return $usedRes;
			}
		}
		return null;
	}
    
    public function getSortedResources()
    {
		if(!is_array($this->sortedResources)) {
			$this->sortResources();
		}
        return $this->sortedResources;
    }
    
    private function sortResources()
    {
		$this->sortedResources = array();
		foreach($this->resources as $resource) {
			$this->sortedResources[$resource->getPov()->getId()][$resource->getLevel()->getId()] = $resource;
		}
	}

    /**
     * Set topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     * @return Matrix
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
