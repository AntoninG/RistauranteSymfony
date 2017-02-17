<?php

namespace DWBD\RistauranteBundle\Form\Type;

use DWBD\RistauranteBundle\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReservationType
 * @package DWBD\RistauranteBundle\Form\Type
 */
class ReservationType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add($builder->create(
				'date', TextType::class, array('trim' => true, 'attr' => array('class' => 'form-control'))
			)->addModelTransformer(new DateTimeToStringTransformer(null, null, 'd/m/Y')))
			->add($builder->create(
				'time', TextType::class, array('trim' => true, 'attr' => array('class' => 'form-control'))
			)->addModelTransformer(new DateTimeToStringTransformer(null, null, 'H:i')))
			->add('number', IntegerType::class, array(
				'label' => 'People',
				'data' => 1,
				'scale' => 0,
				'trim' => true,
				'attr' => array('class' => 'form-control', 'placeholder' => 'Number of people', 'min' => 1, 'max' => 12)
			))
			->add('email', EmailType::class, array(
				'trim' => true,
				'attr' => array('class' => 'form-control')
			))
			->add('name', TextType::class, array(
				'trim' => true,
				'attr' => array('class' => 'form-control')
			))
			->add('phone', TextType::class, array(
				'trim' => true,
				'attr' => array('class' => 'form-control', 'minlength' => 10, 'maxlength' => 16)
			));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Reservation::class
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'dwbd_ristaurantebundle_reservation';
	}

}

