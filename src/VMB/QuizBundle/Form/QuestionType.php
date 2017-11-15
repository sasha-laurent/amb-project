<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question','textarea',array('label'=>'question'))
            ->add('hint','textarea',array('label'=>'indice','required'=>false));
    }
        
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\QuizBundle\Entity\Question',
            'inherit_data' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_question';
    }
}
