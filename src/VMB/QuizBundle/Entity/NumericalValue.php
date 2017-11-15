<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumericalValue
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\NumericalValueRepository")
 */
 
 class NumericalValue extends Question {
     
    /**
     * @var float
     *
     * @ORM\Column(name="val_start", type="float")
     */
    private $valStart = null;
   
    
    /**
     * @var float
     *
     * @ORM\Column(name="val_end", type="float")
     */
    private $valEnd = null;
     
    
    /**
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\Quiz", inversedBy="numericalvalues")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;
 
    
    /**
     * Set valStart
     *
     * @param float $valStart
     * @return NumericalValue
     */
    public function setValStart($valStart)
    {
        $this->valStart = $valStart;

        return $this;
    }

    /**
     * Get valStart
     *
     * @return float 
     */
    public function getValStart()
    {
        return $this->valStart;
    }

    /**
     * Set valEnd
     *
     * @param float $valEnd
     * @return NumericalValue
     */
    public function setValEnd($valEnd)
    {
        $this->valEnd = $valEnd;

        return $this;
    }

    /**
     * Get valEnd
     *
     * @return float 
     */
    public function getValEnd()
    {
        return $this->valEnd;
    }
    
    
    /**
     * Set quiz
     *
     * @param \VMB\QuizBundle\Entity\Quiz $quiz
     * @return Question
     */
    public function setQuiz(\VMB\QuizBundle\Entity\Quiz $quiz = null)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz
     *
     * @return \VMB\QuizBundle\Entity\Quiz 
     */
    public function getQuiz()
    {
        return $this->quiz;
    }
}
