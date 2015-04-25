<?php

namespace VMB\PresentationBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pov
 *
 * @ORM\Table()
 * @ORM\Entity
 * 
 */
class Pov extends MatrixRow
{    
    /**
	* @Gedmo\SortableGroup
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Matrix", inversedBy="povs")
	* @ORM\JoinColumn(nullable=false) 
	*/
	protected $matrix;
}
