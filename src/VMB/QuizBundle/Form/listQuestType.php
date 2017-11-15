<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VMB\QuizBundle\Entity\Question;
use VMB\QuizBundle\Entity\SingleChoice;
use VMB\QuizBundle\Entity\MultiChoice;
use VMB\QuizBundle\Entity\TextArea;
use VMB\QuizBundle\Entity\NumericalValue;

class listQuestType extends AbstractType
{
    private $questions;
    
    public function __construct($questions)
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->questions = $questions;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $i = 0;
        foreach($this->questions as $quest)
        {
            if($quest instanceof SingleChoice)
            {
                $props = $quest->getPropositions();
                $propositions = array();
                foreach($props as $p)
                {                    
                    $propositions[$p->getProposition()]= $p->getProposition();
                }
                $builder->add('question_'.$i,'choice',array(
                'choices'   =>  $propositions,
                'label'     =>  $quest->getQuestion(),
                'required'=>false
                ));
            }
            elseif($quest instanceof MultiChoice)
            {
                $props = $quest->getPropositions();
                $propositions = array();
                foreach($props as $p)
                {                    
                    $propositions[$p->getProposition()]= $p->getProposition();
                }
                $builder->add('question_'.$i,'choice',array(
                'choices' => $propositions,
                'expanded'=> true,
                'multiple'=> true,
                'label'   => $quest->getQuestion(),
                'required'=>false
                ));                       
            }
            if($quest instanceof TextArea)
            {
                $builder->add('question_'.$i,'number', array(
                'label'=>$quest->getQuestion(),
                'required'=>false
                ));
            }
            elseif($quest instanceof NumericalValue)
            {
                $builder->add('question_'.$i,'textarea',array(
                'label' => 'question : '.$quest->getQuestion(),
                'required'  => false
                ));
            }
            $i++;           
        }
    }
        
    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_questions';
    }
}