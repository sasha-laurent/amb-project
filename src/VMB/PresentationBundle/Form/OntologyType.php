<?php

namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OntologyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array('label' => 'form.label.title'));
           
        $ontology = $builder->getData();
		$builder->add('topic', 'entity', array(
		'label' => 'form.label.topic',
		'multiple' => false,
		'disabled' => ($ontology->getTopic() != null),
		'class' => 'VMBPresentationBundle:Topic',
		 'query_builder' => function(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $repo) {
			return $repo->getNodesHierarchyQueryBuilder();
		  }));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\PresentationBundle\Entity\Ontology'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_ontology';
    }
}
