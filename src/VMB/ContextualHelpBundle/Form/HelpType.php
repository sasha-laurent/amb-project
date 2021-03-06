<?php

namespace VMB\ContextualHelpBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HelpType extends AbstractType
{
	
	private $routes;
	
	public function __construct($routes) 
	{
		$this->routes = array();
		foreach($routes as $name => $route) {
			if($name[0] != '_') {
				$this->routes[$name] = $route->getPath();
			}
		}
		asort($this->routes);
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'form.label.title'))
            ->add('text', null, array('label' => 'form.label.text'))
            ->add('route', 'choice', array(
				'label' => 'form.label.associated_routes', 
				'choices' => $this->routes,
				'multiple' => true
			))
			->add('file', 'file', array('label' => 'form.label.video', 'required' => false))
            ->add('save', 'submit', array('label' => 'actions.save'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\ContextualHelpBundle\Entity\Help'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vmb_contextualhelpbundle_help';
    }
}
