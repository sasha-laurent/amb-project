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
	* @ORM\JoinColumn(nullable=false, onDelete="CASCADE") 
	*/
	protected $matrix;

    /**
     * Set matrix
     *
     * @param \VMB\PresentationBundle\Entity\Matrix $matrix
     * @return Pov
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
