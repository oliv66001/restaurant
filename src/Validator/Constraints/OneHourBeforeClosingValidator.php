<?php

namespace App\Validator\Constraints;

use App\Repository\BusinessHoursRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OneHourBeforeClosingValidator extends ConstraintValidator
{
    private $businessHoursRepository;

    public function __construct(BusinessHoursRepository $businessHoursRepository)
    {
        $this->businessHoursRepository = $businessHoursRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Constraints\OneHourBeforeClosing */
        if (null === $value || '' === $value) {
            return;
        }

        $dayOfWeek = $value->format('w'); // 0 (pour dimanche) à 6 (pour samedi)


        // Ajustez pour que 0 corresponde à lundi
        if ($dayOfWeek == 0) {
            $dayOfWeek = 6; // Dimanche
        } else {
            $dayOfWeek -= 1; // Du lundi au samedi
        }

        $businessHours = $this->businessHoursRepository->findOneBy(['day' => $dayOfWeek]);

        if (!$businessHours) {
            $this->context->buildViolation('Le restaurant est fermé ce jour-là.')
                ->addViolation();
            return;
        }



      // Pour les horaires de la matinée
$closingTimeMorning = clone $businessHours->getCloseTime();
$closingTimeMorning->setTimezone(new \DateTimeZone('Europe/Paris'));

// Pour les horaires du soir
$closingTimeEvening = clone $businessHours->getCloseTimeEvening();
$closingTimeEvening->setTimezone(new \DateTimeZone('Europe/Paris'));

$modifiedValue = clone $value;
$modifiedValue->modify('+45 minutes'); 
$modifiedValue->setTimezone(new \DateTimeZone('Europe/Paris'));

// Mise à jour des dates pour la comparaison
$closingTimeMorning->setDate(
    $modifiedValue->format('Y'),
    $modifiedValue->format('m'),
    $modifiedValue->format('d')
);
$closingTimeEvening->setDate(
    $modifiedValue->format('Y'),
    $modifiedValue->format('m'),
    
   
$modifiedValue->format('d')
);

// Validation
if (($closingTimeMorning <= $modifiedValue && $modifiedValue < $businessHours->getOpenTimeEvening()) || $closingTimeEvening <= $modifiedValue) {
    $this->context->buildViolation($constraint->message)
        ->addViolation();
}
    }
}
