<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OneHourBeforeClosing extends Constraint
{
    public $message = 'L\'heure de réservation doit être au moins une heure avant l\'heure de fermeture.';
}
