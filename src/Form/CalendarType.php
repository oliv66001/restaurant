<?php

namespace App\Form;

use Time;
use TimeInterval;
use DateTimeInterface;
use IntlDateFormatter;
use App\Entity\Calendar;
use App\Validator\Constraints\NotMonday;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CalendarType extends AbstractType
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté
        $current_user_id = $this->security->getUser();
        $current_user_name = $current_user_id ? $current_user_id->getLastname() : null;
        $current_user_allergies = $current_user_id ? $current_user_id->getAllergie() : null;


        $builder
            ->add('start', DateTimeType::class, [
                'label' => 'Date et heure de la réservation : ',
                'widget' => 'choice',
                'model_timezone' => 'Europe/Paris',
                'years' => range(date('Y'), date('Y') + 1),
                'hours' => [12, 13, 14, 19, 20, 21],
                'minutes' => [0, 15, 30, 45],
                'attr' => [
                    'class' => 'js-datepicker',
                    'id' => 'calendar_date',
                    'data-provide' => 'datepicker',
                    'data-date-language' => 'fr_FR',
                    'data-date-autoclose' => 'true',
                    'data-date-today-highlight' => 'true',
                    'data-date-week-start' => '1',
                    'data-date-start-date' => '0d',
                    'auto_initialize' => true,
                    'required' => true,
                    'data-date-format' => 'dd/MM/yyyy',
                    'html5' => false,

                ],
                'constraints' => [
                    new GreaterThan([
                        'value' => new \DateTime('today'),
                        'message' => 'L\'heure de début doit être supérieure à l\'heure actuelle',
                    ]),
                    new NotMonday(),
                ]
                
            ])

            ->add('numberOfGuests', IntegerType::class, [
                'label' => 'Nombre de personnes : ',

                'required' => true,
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 12,
                        'minMessage' => 'Vous devez sélectionner au moins 1 personne',
                        'maxMessage' => 'Vous ne pouvez pas sélectionner plus de 12 personnes',
                        'invalidMessage' => 'Vous devez sélectionner au moins 1 personne',
                    ]),
                ]
            ])
            ->add('allergie', TextareaType::class, [
                
                'attr' => [
                    'class' => 'form-control'
                    
                ],
                'label' => 'Allergie',
                'required' => false,
                'data' => $current_user_allergies,
                
            ])
            
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'data' => $current_user_name,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-group',
                    'required' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'user' => null, // Ajoutez une nouvelle option pour l'utilisateur
        ]);
    }
}
