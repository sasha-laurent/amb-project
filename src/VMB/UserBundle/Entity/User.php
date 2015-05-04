<?php
namespace VMB\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
	/**
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	
	public function toString() {
		return $this->getUsername();
	}
	
	
	/**
	* @ORM\ManyToMany(targetEntity="VMB\PresentationBundle\Entity\Presentation")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	*/
	
	
	protected $presentation;
	
	/**
	 * @ORM\ManyToMany(targetEntity="VMB\ResourceBundle\Entity\Resource")
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	
	protected $resource;
	
	
	
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->presentation = new \Doctrine\Common\Collections\ArrayCollection();
        $this->resource = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add presentation
     *
     * @param \VMB\PresentationBundle\Entity\Presentation $presentation
     * @return User
     */
    public function addPresentation(\VMB\PresentationBundle\Entity\Presentation $presentation)
    {
        $this->presentation[] = $presentation;

        return $this;
    }

    /**
     * Remove presentation
     *
     * @param \VMB\PresentationBundle\Entity\Presentation $presentation
     */
    public function removePresentation(\VMB\PresentationBundle\Entity\Presentation $presentation)
    {
        $this->presentation->removeElement($presentation);
    }

    /**
     * Get presentation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Add resource
     *
     * @param \VMB\ResourceBundle\Entity\Resource $resource
     * @return User
     */
    public function addResource(\VMB\ResourceBundle\Entity\Resource $resource)
    {
        $this->resource[] = $resource;

        return $this;
    }

    /**
     * Remove resource
     *
     * @param \VMB\ResourceBundle\Entity\Resource $resource
     */
    public function removeResource(\VMB\ResourceBundle\Entity\Resource $resource)
    {
        $this->resource->removeElement($resource);
    }

    /**
     * Get resource
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    public function presentationIsInCaddy($presentation) {
		foreach($this->presentation as $p) {
			if($p->getId() == $presentation->getId()) {
				return true;
			}
		}
		return false;
	}
	
	public function resourceIsInCaddy($resource) {
		foreach($this->resource as $r) {
			if($r->getId() == $resource->getId()) {
				return true;
			}
		}
		return false;
	}
}
