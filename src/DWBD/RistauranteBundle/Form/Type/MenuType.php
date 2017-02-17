<?php

namespace DWBD\RistauranteBundle\Form\Type;

use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Repository\DishRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
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
				)
			))
			->add('price', NumberType::class, array(
				'label' => 'Price €',
				'required' => true,
				'trim' => true,
				'scale' => 2,
				'attr' => array(
					'class' => 'form-control',
					'placeholder' => 'Amount (10.99)',
					'min' => 2,
					'max' => 3000
				)

			))
			->add('displayOrder', IntegerType::class, array(
				'data' => 1,
				'required' => true,
				'label' => 'Display Order',
				'scale' => 0,
				'attr' => array('class' => 'form-control','min' => 1, 'max' => 45)
			))
			->add('dishes', EntityType::class, array(
				'label' => 'Dishes',
				'class' => 'DWBDRistauranteBundle:Dish',
				'query_builder' => function (DishRepository $r) {
					return $r->createQueryBuilder('d')
						->where('d.state = :state')->setParameter('state', StateEnum::STATE_VALIDATED);
				},
				'choice_label' => function (Dish $dish) {
					return $dish->getTitle();
				},
				'attr' => array('class' => 'form-control'),
				'multiple' => true,
				'required' => false
			));

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
			'data_class' => Menu::class,
			'isEditor' => false,
			'refusedOrValidated' => false
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'dwbd_ristaurantebundle_menu';
	}


}
