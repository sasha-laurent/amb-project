<?php

namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnnotationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('length')
            ->add('text')
            ->add('positionX')
            ->add('positionY')
            ->add('fontSize')
            ->add('fontColor')
            ->add('beginning')
            ->add('presentation', 'entity', array(
				'label' => 'Présentation',
				'property' => 'title',
				'class' => 'VMBPresentationBundle:Presentation')
			)
            ->add('file', 'file', array('label' => 'form.label.thumbnail', 'required' => false))
            ->add('save', 'submit', array('label' => 'actions.save'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\PresentationBundle\Entity\Annotation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_annotation';
    }
}
