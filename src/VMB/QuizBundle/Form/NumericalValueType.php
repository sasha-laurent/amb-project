<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumericalValueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', new QuestionType(), array(
            'data_class' => 'VMB\QuizBundle\Entity\Question'))
            ->add('valStart','number',array('label'=>'Veuillez rentrer la valeur du début'))
            ->add('valEnd','number',array('label'=>'Veuillez rentrer la valeur de fin'));
    }
        
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\QuizBundle\Entity\NumericalValue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_numericalvalue';
    }
}
