<?php

namespace DWBD\RistauranteBundle\Form;

use DWBD\RistauranteBundle\Entity\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
				'label'	   => 'Title'
			))
			->add('description', TextareaType::class, array(
				'label' => 'Description'
			))
			->add('price', MoneyType::class, array(
				'required' => true,
				'currency' => 'EUR',
				'label'    => 'Price'
			))
			->add('homemade', CheckboxType::class, array(
				'data' 	=> true,
				'label' => 'Homemade'
			))
			->add('image', FileType::class, array(
				'label' => 'Image',
				''
			))
			->add('category', ChoiceType::class, array(
				'choices' => CategoryEnum::getCategoriesForForm(),
				'label'   => 'Category'
			));

		if (empty($options['isCreation'])) {
			$builder->add('state', ChoiceType::class, array(
				'choices' => StateEnum::getStatesForForm(),
			));
		}
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Dish::class
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
