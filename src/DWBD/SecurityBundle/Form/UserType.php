<?php

namespace DWBD\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username')
			->add('email')
			->add('password', RepeatedType::class, array(
				'type' => PasswordType::class,
				'invalid_message' => 'The password fields must match.',
				'first_options'	  => array('label' => 'Password'),
				'second_options'  => array('label' => 'Password (validation)'),
			))
			->add('role', ChoiceType::class, array(
				'choices' => RoleEnum::getRolesForForm(),
				'label'	  => 'Role'
			));
	}
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DWBD\RistauranteBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dwbd_securitybundle_user';
    }


}
