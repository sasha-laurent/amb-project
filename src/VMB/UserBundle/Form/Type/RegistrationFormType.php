<?php
namespace VMB\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
       public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['is_admin'] === true)
        {
            $builder->add('uniqueRole', 'choice', array(
                'choices' => array('ROLE_ADMIN' => 'user.role.admin',
                    'ROLE_TEACHER' => 'user.role.teacher',
                    'ROLE_STUDENT' => 'user.role.student'),
                'label' => 'form.label.role',
                'expanded' => false,
                'multiple' => false,
                'mapped' => false
                ));
            $builder->remove('plainPassword')->add('plainPassword', 'hidden', array('data' => 'defaultPwd'));
        } else {
            // Default value is indeed set, 
            // and options[is_admin] always equals to false
            if (array_key_exists("is_admin", $options)){
                echo 'Set!';
            } else {
                echo 'Not Set!';
            }
        }
        $builder->add('save', 'submit', array('label' => 'actions.add'));
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'is_admin'));
        $resolver->setDefaults(array(
            'is_admin' => false,
            'data_class' => 'VMB\UserBundle\Entity\User',
            'intention'  => 'registration',
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
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
