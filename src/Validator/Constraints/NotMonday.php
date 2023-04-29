<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotMonday extends Constraint
{
    public $message = 'Les réservations ne sont pas possibles le lundi.';
}