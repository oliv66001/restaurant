<?php

namespace App\Form;

use App\Entity\BusinessHours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BusinessHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, [
                'label' => 'Jour : ',
                'choices' => [
                    'Dimanche' => 0,
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                   
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('openTime', TimeType::class, [
                'widget' => 'choice',
                'input'  => 'datetime',
                'with_seconds' => false,
            ])
            ->add('closeTime', TimeType::class, [
                'widget' => 'choice',
                'input'  => 'datetime',
                'with_seconds' => false,
            ])
            ->add('openTimeEvening', TimeType::class, [
                'widget' => 'choice',
                'input'  => 'datetime',
                'with_seconds' => false,
            ])
            ->add('closeTimeEvening', TimeType::class, [
                'widget' => 'choice',
                'input'  => 'datetime',
                'with_seconds' => false,
            ])
            ->add('closed', ChoiceType::class, [
                'label' => 'Fermé ?',
                'choices' => [
                    'Non' => false,
                    'Oui' => true
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BusinessHours::class,
        ]);
    }

    private function generateTimeChoices(): array
    {
        $timeChoices = [];

        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 60; $j += 15) {
                $time = sprintf("%02d:%02d", $i, $j);
                $timeChoices[$time] = $time;
            }
        }

        return $timeChoices;
    }
}
