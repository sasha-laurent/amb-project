<?php
namespace VMB\PresentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LevelType extends AbstractType
{
	public function getParent()
	{
		return new MatrixRowType();
	}
	
	/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VMB\PresentationBundle\Entity\Level'
        ));
    }
	
	/**
     * @return string
     */
    public function getName()
    {
        return 'vmb_presentationbundle_level';
    }
}
