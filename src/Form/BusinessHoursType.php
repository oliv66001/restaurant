<?php

namespace App\Form;

use App\Entity\BusinessHours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BusinessHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, [
                'label' => 'Jour : ',
                'choices' => [
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('openTime', ChoiceType::class, [
                'label' => 'Heure d\'ouverture',
                'choices' => $this->generateTimeChoices(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('closeTime', ChoiceType::class, [
                'label' => 'Heure de fermeture',
                'choices' => $this->generateTimeChoices(),
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
