<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SingleChoice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\SingleChoiceRepository")
 */
class SingleChoice extends Question
{
    
    /**
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\Quiz", inversedBy="singlechoices")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\Proposition", mappedBy="singlechoice", cascade={"remove", "persist"}, orphanRemoval=true)
	*/
    private $propositions;

    
    public function __construct()
    {
        $this->propositions = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set quiz
     *
     * @param \VMB\QuizBundle\Entity\Quiz $quiz
     * @return SingleChoice
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

    /**
     * Add propositions
     *
     * @param \VMB\QuizBundle\Entity\Proposition $propositions
     * @return SingleChoice
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
}
