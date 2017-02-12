<?php

namespace DWBD\RistauranteBundle\Form;

use DWBD\RistauranteBundle\Entity\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Form\DataTransformer\StringToArrayAllergensTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DishType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', TextType::class, array(
					'required' => true,
					'label' => 'Title',
					'trim' => true,
					'attr' => array(
						'class' => 'form-control',
						'placeholder' => 'Name of your dish'
					),
				)
			)
			->add('description', TextareaType::class, array(
					'label' => 'Description',
					'trim' => true,
					'required' => false,
					'attr' => array(
						'class' => 'form-control',
						'placeholder' => 'A short description',
						'maxlength' => 1000,
						'rows' => 5
					),
				)
			)
			->add('price', NumberType::class, array(
					'label' => 'Price €',
					'required' => true,
					'trim' => true,
					'scale' => 2,
					'attr' => array(
						'class' => 'form-control',
						'placeholder' => 'Amount (10.99)'
					),
				)
			)
			->add('homemade', CheckboxType::class, array(
					'data' => false,
					'label' => 'Homemade',
					'required' => false
				)
			)
			->add('image', FileType::class, array(
					'label' => 'Image',
					'trim' => true,
					'required' => false
				)
			)
			->add('category', ChoiceType::class, array(
					'choices' => CategoryEnum::getCategoriesForForm(),
					'label' => 'Category',
					'attr' => array('class' => 'form-control'),
					'required' => false
				)
			)
			->add($builder->create(
				'allergens', TextareaType::class, array(
					'required' => false,
					'trim' => true,
					'label' => 'Allergens',
					'attr' => array(
						'class' => 'form-control',
						'placeholder' => 'Put here the allergens, separated by a line return',
						'maxlength' => 1000,
						'rows' => 5
					)
				)
			)->addModelTransformer(new StringToArrayAllergensTransformer()));

		if (empty($options['refusedOrValidated'])) {
			$states = StateEnum::getStatesForForm();
			if (!empty($options['isEditor'])) {
				$flip = array_flip($states);
				unset($flip[StateEnum::STATE_REFUSED], $flip[StateEnum::STATE_VALIDATED]);
				$states = array_flip($flip);
			}

			$builder->add('state', ChoiceType::class, array(
					'choices' => $states,
					'attr' => array('class' => 'form-control'),
				)
			);
		}

	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Dish::class,
			'isEditor' => false,
			'refusedOrValidated' => false
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'dwbd_ristaurantebundle_dish';
	}


}
