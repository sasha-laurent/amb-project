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
	* @ORM\JoinColumn(nullable=false) 
	*/
	private $presentation;
	
	/**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\UsedResource")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $usedResource;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;


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
}
