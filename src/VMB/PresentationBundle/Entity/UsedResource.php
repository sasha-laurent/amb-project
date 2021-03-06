<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsedResource
 *
 * @ORM\Table(name="usedresource")
 * @ORM\Entity(repositoryClass="VMB\PresentationBundle\Entity\UsedResourceRepository")
 */
class UsedResource
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
	* @ORM\ManyToOne(targetEntity="VMB\ResourceBundle\Entity\Resource")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	private $resource;

    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Level")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	private $level;

    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Pov")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	private $pov;
	
	/**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Matrix", inversedBy="resources")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $matrix;
	
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
     * Constructor
     */
    public function __construct($matrix)
    {
        $this->resource = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setMatrix($matrix);
    }

    /**
     * Set level
     *
     * @param \VMB\PresentationBundle\Entity\Level $level
     * @return UsedResource
     */
    public function setLevel(\VMB\PresentationBundle\Entity\Level $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \VMB\PresentationBundle\Entity\Level 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set pov
     *
     * @param \VMB\PresentationBundle\Entity\Pov $pov
     * @return UsedResource
     */
    public function setPov(\VMB\PresentationBundle\Entity\Pov $pov = null)
    {
        $this->pov = $pov;

        return $this;
    }

    /**
     * Get pov
     *
     * @return \VMB\PresentationBundle\Entity\Pov 
     */
    public function getPov()
    {
        return $this->pov;
    }

    /**
     * Set matrix
     *
     * @param \VMB\PresentationBundle\Entity\Matrix $matrix
     * @return UsedResource
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

    /**
     * Set resource
     *
     * @param \VMB\ResourceBundle\Entity\Resource $resource
     * @return UsedResource
     */
    public function setResource(\VMB\ResourceBundle\Entity\Resource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \VMB\ResourceBundle\Entity\Resource 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return UsedResource
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
}
