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
        $builder
            ->add('title', null, array('label' => 'Titre'))
            ->add('description', null, array('label' => 'Description'))
            ->add('povs', 'collection', array(
				'label'		   => 'Points de vues',
				'type'         => new MatrixRowType(),
				'allow_add'    => true,
				'allow_delete' => true
			  ))
			->add('levels', 'collection', array(
				'label'		   => 'Niveaux',
				'type'         => new MatrixRowType(),
				'allow_add'    => true,
				'allow_delete' => true
			  ))
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
