<?php

namespace VMB\ResourceBundle\Form;

use VMB\QuizBundle\Form\QuizType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResourceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
     
    private $isTeacher = false;
    
    public function __construct($isTeacher)
    {
        $this->isTeacher = $isTeacher;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resource = $builder->getData();

        $builder
            ->add('title', null, array('label' => 'form.label.title'))
            ->add('description', null, array('label' => 'form.label.description'))
            ->add('keywords', null, array('label' => 'form.label.keywords'));
		$builder->add('topic', 'entity', array(
			'label' => 'form.label.topic',
			'multiple' => true,
			'class' => 'VMBPresentationBundle:Topic',
			'query_builder' => function(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $repo) {
				return $repo->getNodesHierarchyQueryBuilder();
			  }
		));
		
		if($resource->getId() == null) {
			$builder->add('file', 'file', array('label' => 'form.label.file'));
		}
        // Optional thumbnail for audio file (must be initialized as hidden)
		$builder->add('customAudioArt', 'file', array('label' => 'form.label.thumbnail', 'required' => false));
		if($this->isTeacher)
            $builder->add('quiz', new QuizType(), array('label' => 'quiz'));
        $builder->add('save', 'submit', array('label' => 'actions.save'));
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
