<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultiChoiceType extends AbstractType
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
            ->add('propositions', 'collection', array(
				'by_reference' => false,
				'label'		   => 'Propositions',
				'type'         => new PropositionType(),
				'allow_add'    => true,
				'allow_delete' => true
			  ));
    }
        
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\QuizBundle\Entity\MultiChoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_multichoice';
    }
}
