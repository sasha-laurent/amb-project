<?php

namespace VMB\ForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class discussionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder           
            ->add('title', 'text', array('label' => 'form.label.title'))
            ->add('topic', 'entity', array(
			'label' => 'form.label.topic',
			'class' => 'VMBPresentationBundle:Topic',
		))
            ->add('description', 'textarea', array('label' => 'form.label.description','attr'=>array('class'=>'ckeditor')))
            ->add('save','submit',array('label' => 'form.label.submit'))
            ;
    }
    

    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\ForumBundle\Entity\discussion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_forumbundle_discussion';
    }
}
