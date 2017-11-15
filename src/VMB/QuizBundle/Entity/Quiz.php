<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quiz
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\QuizRepository")
 */
class Quiz
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
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\MultiChoice", mappedBy="quiz", cascade={"persist"})
	*/
    private $multichoices;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\SingleChoice", mappedBy="quiz", cascade={"persist"})
	*/
    private $singlechoices;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\TextArea", mappedBy="quiz", cascade={"persist"})
	*/
    private $textareas;
    /**
     *
     * @ORM\OneToMany(targetEntity="VMB\QuizBundle\Entity\NumericalValue", mappedBy="quiz", cascade={"persist"})
	*/
    private $numericalvalues;

    public function __construct()
    {
        $this->multichoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->singlechoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->textareas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->numericalvalues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add multichoices
     *
     * @param \VMB\QuizBundle\Entity\MultiChoice $multichoices
     * @return Quiz
     */
    public function addMultichoice(\VMB\QuizBundle\Entity\MultiChoice $multichoices)
    {
        $this->multichoices[] = $multichoices;

        return $this;
    }

    /**
     * Remove multichoices
     *
     * @param \VMB\QuizBundle\Entity\MultiChoice $multichoices
     */
    public function removeMultichoice(\VMB\QuizBundle\Entity\MultiChoice $multichoices)
    {
        $this->multichoices->removeElement($multichoices);
    }

    /**
     * Get multichoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMultichoices()
    {
        return $this->multichoices;
    }

    /**
     * Add singlechoices
     *
     * @param \VMB\QuizBundle\Entity\SingleChoice $singlechoices
     * @return Quiz
     */
    public function addSinglechoice(\VMB\QuizBundle\Entity\SingleChoice $singlechoices)
    {
        $this->singlechoices[] = $singlechoices;

        return $this;
    }

    /**
     * Remove singlechoices
     *
     * @param \VMB\QuizBundle\Entity\SingleChoice $singlechoices
     */
    public function removeSinglechoice(\VMB\QuizBundle\Entity\SingleChoice $singlechoices)
    {
        $this->singlechoices->removeElement($singlechoices);
    }

    /**
     * Get singlechoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSinglechoices()
    {
        return $this->singlechoices;
    }

    /**
     * Add textareas
     *
     * @param \VMB\QuizBundle\Entity\TextArea $textareas
     * @return Quiz
     */
    public function addTextarea(\VMB\QuizBundle\Entity\TextArea $textareas)
    {
        $this->textareas[] = $textareas;

        return $this;
    }

    /**
     * Remove textareas
     *
     * @param \VMB\QuizBundle\Entity\TextArea $textareas
     */
    public function removeTextarea(\VMB\QuizBundle\Entity\TextArea $textareas)
    {
        $this->textareas->removeElement($textareas);
    }

    /**
     * Get textareas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTextareas()
    {
        return $this->textareas;
    }

    /**
     * Add numericalvalues
     *
     * @param \VMB\QuizBundle\Entity\NumericalValue $numericalvalues
     * @return Quiz
     */
    public function addNumericalvalue(\VMB\QuizBundle\Entity\NumericalValue $numericalvalues)
    {
        $this->numericalvalues[] = $numericalvalues;

        return $this;
    }

    /**
     * Remove numericalvalues
     *
     * @param \VMB\QuizBundle\Entity\NumericalValue $numericalvalues
     */
    public function removeNumericalvalue(\VMB\QuizBundle\Entity\NumericalValue $numericalvalues)
    {
        $this->numericalvalues->removeElement($numericalvalues);
    }

    /**
     * Get numericalvalues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNumericalvalues()
    {
        return $this->numericalvalues;
    }
}
