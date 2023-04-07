<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,
            [
                
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'E-mail',
                'required' => true,
            ]
        )
            ->add('lastname', TextType::class,
            [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'required' => true,
            ]
        )
            ->add('firstname', TextType::class,
            [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prénom',
                'required' => true,
            ]
        )
            ->add('allergie',  TextareaType::class,
            [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Allergie',
                'required' => false
            
        ])
            ->add('phone',TextType::class,
            [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Téléphone',
                'required' => true,
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
