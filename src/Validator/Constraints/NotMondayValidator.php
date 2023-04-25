<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotMondayValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTimeInterface) {
            return;
        }
       // 1= Monday, 7=Sunday
        if ($value->format('N') == 1) { 
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}