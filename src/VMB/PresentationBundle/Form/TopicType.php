<?php

namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label'=>'form.label.title', 'attr' => array('autofocus' => 1)))
            ->add('parent',  'entity', array(
				'class' => 'VMB\PresentationBundle\Entity\Topic', 
				'empty_value' => 'topic.new_root',
				'required' => false,
				 'query_builder' => function(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $repo) {
					return $repo->getNodesHierarchyQueryBuilder();
				  }
				))
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
            'data_class' => 'VMB\PresentationBundle\Entity\Topic'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_topic';
    }
}
