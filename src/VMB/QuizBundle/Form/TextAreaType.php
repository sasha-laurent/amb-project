<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextAreaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('propositions')
            ->add('question', new QuestionType(), array(
            'data_class' => 'VMB\QuizBundle\Entity\Question'))
            ->add('solution','textarea',array('label'=>'Veuillez rentrer la réponse'));
    }
        
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\QuizBundle\Entity\TextArea'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_textarea';
    }
}
