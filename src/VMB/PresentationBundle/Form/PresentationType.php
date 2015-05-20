<?php

namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PresentationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label'=> 'Titre'))
            ->add('description', null, array('label'=> 'Description'))
            ->add('duration', 'hidden')
            ->add('file', 'file', array('label' => 'Miniature'))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\PresentationBundle\Entity\Presentation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_presentation';
    }
}
