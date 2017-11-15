<?php

namespace VMB\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proposition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\QuizBundle\Entity\PropositionRepository")
 */
class Proposition
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
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\SingleChoice", inversedBy="propositions")
     * @ORM\JoinColumn(name="singlechoice_id", referencedColumnName="id")
     *
     */
    private $singlechoice;
    
    /**
     * @ORM\ManyToOne(targetEntity="VMB\QuizBundle\Entity\MultiChoice", inversedBy="propositions")
     * @ORM\JoinColumn(name="multichoice_id", referencedColumnName="id")
     *
     */
     private $multichoice;
     
    /**
     * @var string
     *
     * @ORM\Column(name="proposition", type="string", length=255)
     */
    private $proposition;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCorrect", type="boolean")
     */
    private $isCorrect = false;


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
     * Set proposition
     *
     * @param string $proposition
     * @return Proposition
     */
    public function setProposition($proposition)
    {
        $this->proposition = $proposition;

        return $this;
    }

    /**
     * Get proposition
     *
     * @return string 
     */
    public function getProposition()
    {
        return $this->proposition;
    }

    /**
     * Set isCorrect
     *
     * @param boolean $isCorrect
     * @return Proposition
     */
    public function setIsCorrect($isCorrect)
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * Get isCorrect
     *
     * @return boolean 
     */
    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

    /**
     * Set singlechoice
     *
     * @param \VMB\QuizBundle\Entity\SingleChoice $singlechoice
     * @return Proposition
     */
    public function setSinglechoice(\VMB\QuizBundle\Entity\SingleChoice $singlechoice = null)
    {
        $this->singlechoice = $singlechoice;

        return $this;
    }

    /**
     * Get singlechoice
     *
     * @return \VMB\QuizBundle\Entity\SingleChoice 
     */
    public function getSinglechoice()
    {
        return $this->singlechoice;
    }

    /**
     * Set multichoice
     *
     * @param \VMB\QuizBundle\Entity\MultiChoice $multichoice
     * @return Proposition
     */
    public function setMultichoice(\VMB\QuizBundle\Entity\MultiChoice $multichoice = null)
    {
        $this->multichoice = $multichoice;

        return $this;
    }

    /**
     * Get multichoice
     *
     * @return \VMB\QuizBundle\Entity\MultiChoice 
     */
    public function getMultichoice()
    {
        return $this->multichoice;
    }
}
