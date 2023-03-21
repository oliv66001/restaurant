<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UsersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'E-mail'
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Nom'
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Prénom'
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Téléphone'
                ]
            )
            ->add(
                'allergie',
                TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Allergie',
                    'required' => false
                
            ]);
           
               
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
