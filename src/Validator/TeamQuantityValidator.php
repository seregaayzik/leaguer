<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamQuantityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint):void
    {
        $count = count($value);

        if ($count % 2 !== 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
