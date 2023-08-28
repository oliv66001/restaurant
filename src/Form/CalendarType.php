<?php

namespace App\Form;

use Time;
use TimeInterval;
use DateTimeInterface;
use IntlDateFormatter;
use App\Entity\Calendar;
use Symfony\Component\Form\FormEvent;
use App\Repository\CalendarRepository;
use Symfony\Component\Form\FormEvents;
use App\Validator\Constraints\NotMonday;
use Symfony\Component\Form\AbstractType;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use App\Validator\Constraints\OneHourBeforeClosing;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CalendarType extends AbstractType
{
    private $security;
    private $calendarRepository;
    private $businessHoursRepository;

    
    public function __construct(Security $security, CalendarRepository $calendarRepository, BusinessHoursRepository $businessHoursRepository)
    {
        $this->security = $security;
        $this->calendarRepository = $calendarRepository;
        $this->businessHoursRepository = $businessHoursRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    // Récupérer l'utilisateur connecté
    $current_user_id = $this->security->getUser();
    $current_user_name = $current_user_id ? $current_user_id->getLastname() : null;
    $current_user_allergies = $current_user_id ? $current_user_id->getAllergie() : null;

    
    // Récupérer les heures d'ouverture
    $business_hours = $options['hours'] ?? [];
   
        
        $builder
            ->add('start', DateTimeType::class, [
                'label' => 'Date et heure de la réservation : ',
                'widget' => 'choice',
                'model_timezone' => 'Europe/Paris',
                'years' => range(date('Y'), date('Y') + 1),
                'hours' => $options['hours'],
                'minutes' => [0, 15, 30, 45],
                'format' => 'dd/MM/yyyy HH:mm',
                'html5' => false,
                'constraints' => [
                    new GreaterThan([
                        'value' => new \DateTime('today'),
                        'message' => 'L\'heure de début doit être supérieure à l\'heure actuelle',
                    ]),
                    new OneHourBeforeClosing(),
                ]
            ])

            ->add('numberOfGuests', ChoiceType::class, [
                'label' => 'Nombre de personnes : ',
                'attr' => [
                    'id' => 'calendar_numberOfGuests',
                ],
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    
                   
            '10' => 10,
                    '11' => 11,
                    '12' => 12,
                ],
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

            ->add('allergie', TextType::class, [
                'label' => 'Allergie : ',
                'data' => $current_user_allergies,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-group',
                    'required' => false,
                ],


            ])

            ->add('allergieOfGuests', TextareaType::class, [
                'label' => 'Allergie des invités : ',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'

                ],

            ])

            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'data' => $current_user_name,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-group',
                    'required' => false,
                ],
            ])

            ->add('availablePlaces', IntegerType::class, [
                'label' => 'Places disponibles : ',
                'disabled' => true,
                'attr' => [
                    'required' => false,
                    'id' => 'calendar_availablePlaces',
                ],
            ]);

       
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'user' => null, 
            'hours' => [], 
        ]);
    }

}