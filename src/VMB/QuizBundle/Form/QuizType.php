<?php

namespace VMB\QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuizType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $quiz = $builder->getData();
		$builder
            ->add('multichoices', 'collection', array(
				'by_reference' => false,
				'label'		   => 'choix multiple',
				'type'         => new MultiChoiceType(),
				'allow_add'    => true,
				'allow_delete' => true,
                'required'     => false 
			  ))
              ->add('singlechoices', 'collection', array(
				'by_reference' => false,
				'label'		   => 'choix unique',
				'type'         => new SingleChoiceType(),
				'allow_add'    => true,
				'allow_delete' => true,
                'required'     => false
			  ))
              ->add('textareas', 'collection', array(
				'by_reference' => false,
				'label'		   => 'champ texte',
				'type'         => new TextAreaType(),
				'allow_add'    => true,
				'allow_delete' => true,
                'required'     => false
			  ))
              ->add('numericalvalues', 'collection', array(
				'by_reference' => false,
				'label'		   => 'valeur numérique',
				'type'         => new NumericalValueType(),
				'allow_add'    => true,
				'allow_delete' => true,
                'required'     => false
			  ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\QuizBundle\Entity\Quiz'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_quizbundle_quiz';
    }
}
