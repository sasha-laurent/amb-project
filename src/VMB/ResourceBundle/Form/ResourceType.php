<?php

namespace VMB\ResourceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResourceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resource = $builder->getData();

        $builder
            ->add('title', null, array('label' => 'Titre'))
            ->add('description', null, array('label' => 'Description'))
            ->add('keywords', null, array('label' => 'Mots-clés'));
		$builder->add('topic', 'entity', array(
			'label' => 'Thématique',
			'multiple' => true,
			'class' => 'VMBPresentationBundle:Topic',
			 'query_builder' => function(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $repo) {
				return $repo->getNodesHierarchyQueryBuilder();
			  }
		));
		
		if($resource->getTopic() == null) {
			$builder->add('file', 'file', array('label' => 'Fichier'));
		}
		
		$builder->add('Sauvegarder', 'submit');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\ResourceBundle\Entity\Resource'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_resourcebundle_resource';
    }
}
