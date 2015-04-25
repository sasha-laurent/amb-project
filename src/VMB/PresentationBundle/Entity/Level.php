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

    /**
     * Set matrix
     *
     * @param \VMB\PresentationBundle\Entity\Matrix $matrix
     * @return Level
     */
    public function setMatrix(\VMB\PresentationBundle\Entity\Matrix $matrix)
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
}
