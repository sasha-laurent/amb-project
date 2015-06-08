<?php
namespace VMB\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->remove('plainPassword');
        $builder->add('uniqueRole', 'choice', array(
                'choices' => array('ROLE_ADMIN' => 'user.role.admin',
				'ROLE_TEACHER' => 'user.role.teacher',
				'ROLE_STUDENT' => 'user.role.student'),
                'label' => 'form.label.role',
                'expanded' => false,
                'multiple' => false,
                'mapped' => false
            ))
            ->add('plainPassword', 'hidden', array('data' => 'defaultPwd'))
            ->add('save', 'submit', array('label' => 'actions.add'));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'vmb_user_registration';
    }
}
