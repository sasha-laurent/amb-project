<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TextArea
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\TextAreaRepository")
 */
class TextArea extends Question
{

    /**
     * @var string
     *
     * @ORM\Column(name="solution", type="string", length=255)
     */
    private $solution;
    
    /**
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\Quiz", inversedBy="textareas")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;

    /**
     * Set solution
     *
     * @param string $solution
     * @return TextArea
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;

        return $this;
    }

    /**
     * Get solution
     *
     * @return string 
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Set quiz
     *
     * @param \VMB\QuizBundle\Entity\Quiz $quiz
     * @return TextArea
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
