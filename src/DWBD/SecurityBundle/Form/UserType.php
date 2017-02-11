<?php

namespace DWBD\SecurityBundle\Form;

use DWBD\SecurityBundle\Entity\RoleEnum;
use DWBD\SecurityBundle\Entity\User;
use DWBD\SecurityBundle\Form\DataTransformer\StringToArrayUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', TextType::class, array(
			'attr' => array('class' => 'form-control', 'placeholder' => 'Login')
		))
		->add('email', TextType::class, array(
			'attr' => array('class' => 'form-control', 'placeholder' => 'yourname@domaine.com'),
		))
		->add('password', RepeatedType::class, array(
			'type' => PasswordType::class,
			'invalid_message' => 'The password fields must match.',
			'first_options'	  => array(
				'label' => 'Password',
				'attr' => array('class' => 'form-control')
			),
			'second_options'  => array(
				'label' => 'Password (validation)',
				'attr' => array('class' => 'form-control')
			)
		))
		->add($builder->create(
				'role', ChoiceType::class, array(
				'choices' => RoleEnum::getRolesForForm(),
				'label'	  => 'Role',
				'attr' => array('class' => 'form-control')
			))->addModelTransformer(new StringToArrayUserTransformer())
		);
	}

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
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
