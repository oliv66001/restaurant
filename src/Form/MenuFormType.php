<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Dishes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'Nom du menu',
                    'attr' => [
                        'placeholder' => 'Nom du menu',
                        'class'=> "form-control"
                    ],
                ]
            )
            ->add('description', null,
                [
                    'label' => 'Description du menu',
                    'attr' => [
                        'placeholder' => 'Description du menu',
                        'class'=> "form-control"
                    ]
                ]
            )
            ->add('price', null,
                [
                    'label' => 'Prix du menu',
                    'attr' => [
                        'placeholder' => 'Prix du menu',
                        'class'=> "form-control"
                    ]
                ]
            )
            ->add('dishes', EntityType::class, [
                'class' => Dishes::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'label' => 'Plats du menu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
