<?php

namespace DWBD\RistauranteBundle\Form;

use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\SecurityBundle\Entity\RoleEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
				'label'	   => 'Title'
			))
			->add('price', MoneyType::class, array(
				'required' => true,
				'currency' => 'EUR',
				'label'	   => 'Price'

			))
			->add('displayOrder', IntegerType::class, array(
				'required' => true,
				'label'	   => 'Display Order',
				'scale'    => 0
			))
			->add('dishes', EntityType::class, array(
				'label'		=> 'Dishes',
				'class' 	=> 'DWBDRistauranteBundle:Dish',
				'choice_label' => function(Dish $dish) {
					return $dish->getTitle();
				},
				'multiple'	=> true
			));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DWBD\RistauranteBundle\Entity\Menu'
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
