<?php

namespace VMB\PresentationBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Level
 *
 * @ORM\Table()
 * @ORM\Entity
 * 
 */
class Level extends MatrixRow
{
    /**
	* @Gedmo\SortableGroup
	* @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Matrix", inversedBy="levels")
	* @ORM\JoinColumn(nullable=false) 
	*/
	protected $matrix;
}
