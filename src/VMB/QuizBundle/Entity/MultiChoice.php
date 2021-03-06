<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MultiChoice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\MultiChoiceRepository")
 */
class MultiChoice extends Question
{
    
     
    /**
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\Proposition", mappedBy="multichoice", cascade={"remove", "persist"}, orphanRemoval=true)
	*/
    private $propositions;
 
    
    /**
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\Quiz", inversedBy="multichoices")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->propositions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add propositions
     *
     * @param \VMB\QuizBundle\Entity\Proposition $propositions
     * @return MultiChoice
     */
    public function addProposition(\VMB\QuizBundle\Entity\Proposition $propositions)
    {
        $this->propositions[] = $propositions;

        return $this;
    }

    /**
     * Remove propositions
     *
     * @param \VMB\QuizBundle\Entity\Proposition $propositions
     */
    public function removeProposition(\VMB\QuizBundle\Entity\Proposition $propositions)
    {
        $this->propositions->removeElement($propositions);
    }

    /**
     * Get propositions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPropositions()
    {
        return $this->propositions;
    }
    
    
    /**
     * Set quiz
     *
     * @param \VMB\QuizBundle\Entity\Quiz $quiz
     * @return Question
     */
    public function setQuiz(\VMB\QuizBundle\Entity\Quiz $quiz)
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
