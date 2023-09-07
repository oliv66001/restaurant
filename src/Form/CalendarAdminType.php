<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CalendarAdminType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $current_user_id = $this->security->getUser();
        $calendar = $builder->getData();
        $reserving_user = $calendar->getName(); // Assure-toi d'avoir une méthode getUser() dans ton entité Calendar
        $reserving_user_name = $reserving_user ? $reserving_user->getLastname() : null;
        $current_user_allergies = $current_user_id ? $current_user_id->getAllergie() : null;

        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom : ',
            'data' => $reserving_user_name,
            'disabled' => true,
            'attr' => [
                'class' => 'form-group',
                'required' => false,
            ],
        ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('numberOfGuests', IntegerType::class)
           
            ->add('availablePlaces', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
