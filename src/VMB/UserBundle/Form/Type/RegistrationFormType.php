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
        if(array_key_exists("is_admin", $options) 
            && $options['is_admin'] == true)
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
        } else {
            dump($options);
            // as default value set, options[admin] always equals to false
        }
        // add your custom field
        $builder->remove('plainPassword');
        $builder->add('plainPassword', 'hidden', array('data' => 'defaultPwd'))
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

   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('is_admin' => false)); 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
       $this->configureOptions($resolver);
    }
}
