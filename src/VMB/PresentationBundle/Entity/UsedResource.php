<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsedResource
 *
 * @ORM\Table()
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
	* @ORM\ManyToMany(targetEntity="VMB\ResourceBundle\Entity\Resource")
	*/
	private $resource;

    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Level")
	*/
	private $level;

    /**
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Pov")
	*/
	private $pov;
	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
