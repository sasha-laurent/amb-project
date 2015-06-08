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
        $builder->add('title', null, array('label' => 'form.label.title'));
        
        $matrix = $builder->getData();
        if($matrix->getTopic() == null) {
			$builder->add('topic', 'entity', array(
			'label' => 'form.label.topic',
			'multiple' => false,
			'class' => 'VMBPresentationBundle:Topic',
			 'query_builder' => function(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $repo) {
				return $repo->getNodesHierarchyQueryBuilder();
			  }));
		}
           
        $builder
            ->add('description', null, array('label' => 'form.label.description'))
			->add('save', 'submit', array('label' => 'actions.save'))
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
