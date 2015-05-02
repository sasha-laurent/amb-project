<?php

namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatrixType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, array('label' => 'Titre'));
        
        $matrix = $builder->getData();
        if($matrix->getTopic() == null) {
			$builder->add('topic', 'entity', array(
					'class' => 'VMBPresentationBundle:Topic',
					'property' => 'title'
				));
		}
           
        $builder
            ->add('description', null, array('label' => 'Description'))
            ->add('povs', 'collection', array(
				'by_reference' => false,
				'label'		   => 'Points de vues',
				'type'         => new PovType(),
				'allow_add'    => true,
				'allow_delete' => false
			  ))
			->add('levels', 'collection', array(
				'by_reference' => false,
				'label'		   => 'Niveaux',
				'type'         => new LevelType(),
				'allow_add'    => true,
				'allow_delete' => false
			  ))
			->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\PresentationBundle\Entity\Matrix'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_matrix';
    }
}
