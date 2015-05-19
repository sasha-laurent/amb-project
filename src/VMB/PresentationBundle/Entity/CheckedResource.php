<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckedResource
 *
 * @ORM\Table(name="checkedresource",uniqueConstraints={@ORM\UniqueConstraint(name="uniq_usedresource", columns={"usedResource_id", "presentation_id"})})
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\CheckedResourceRepository")
 */
class CheckedResource
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
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Presentation", inversedBy="resources")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE") 
	*/
	private $presentation;
	
	/**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\UsedResource")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	private $usedResource;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="suggested", type="boolean")
     */
    private $suggested;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration = 11;


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
     * Set sort
     *
     * @param integer $sort
     * @return CheckedResource
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set presentation
     *
     * @param \VMB\PresentationBundle\Entity\Presentation $presentation
     * @return CheckedResource
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

    /**
     * Set usedResource
     *
     * @param \VMB\PresentationBundle\Entity\UsedResource $usedResource
     * @return CheckedResource
     */
    public function setUsedResource(\VMB\PresentationBundle\Entity\UsedResource $usedResource)
    {
        $this->usedResource = $usedResource;

        return $this;
    }

    /**
     * Get usedResource
     *
     * @return \VMB\PresentationBundle\Entity\UsedResource 
     */
    public function getUsedResource()
    {
        return $this->usedResource;
    }

    /**
     * Set suggested
     *
     * @param boolean $suggested
     * @return CheckedResource
     */
    public function setSuggested($suggested)
    {
        $this->suggested = $suggested;

        return $this;
    }

    /**
     * Get suggested
     *
     * @return boolean 
     */
    public function getSuggested()
    {
        return $this->suggested;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return CheckedResource
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
		if($this->usedResource->getResource()->getType() == 'video' || $this->usedResource->getResource()->getType() == 'audio') {
			return $this->usedResource->getResource()->getDuration();
		}
		else {
			return $this->duration;
		}
    }
}
