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
            ->add('title', null, array('label' => 'Titre'))
            ->add('text', null, array('label' => 'Texte'))
            ->add('route', 'choice', array(
				'label' => 'Adresses associées', 
				'choices' => $this->routes,
				'multiple' => true
			))
			->add('file', 'file', array('label' => 'Vidéo', 'required' => false))
            ->add('save', 'submit', array('label' => 'Sauvegarder'))
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
