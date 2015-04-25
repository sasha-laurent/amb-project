<?php

namespace VMB\PresentationBundle\Entity;

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
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="date")
     */
    private $dateUpdate;

	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\Pov", mappedBy="matrix", cascade={"remove"})
	*/
	private $povs;
	
	
	/**
	* @ORM\OneToMany(targetEntity="VMB\PresentationBundle\Entity\Level", mappedBy="matrix", cascade={"remove"})
	*/
	private $levels;

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
     * @param \VMB\PresentationBundle\Entity\MatrixRow $povs
     * @return Matrix
     */
    public function addPov(\VMB\PresentationBundle\Entity\MatrixRow $povs)
    {
        $this->povs[] = $povs;

        return $this;
    }

    /**
     * Remove povs
     *
     * @param \VMB\PresentationBundle\Entity\MatrixRow $povs
     */
    public function removePov(\VMB\PresentationBundle\Entity\MatrixRow $povs)
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
     * @param \VMB\PresentationBundle\Entity\MatrixRow $levels
     * @return Matrix
     */
    public function addLevel(\VMB\PresentationBundle\Entity\MatrixRow $levels)
    {
        $this->levels[] = $levels;

        return $this;
    }

    /**
     * Remove levels
     *
     * @param \VMB\PresentationBundle\Entity\MatrixRow $levels
     */
    public function removeLevel(\VMB\PresentationBundle\Entity\MatrixRow $levels)
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
}