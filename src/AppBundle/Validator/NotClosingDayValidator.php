<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 05/02/2018
 * Time: 14:12
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotClosingDayValidator extends ConstraintValidator
{
    const TUESDAY = 2;
    const SUNDAY = 7;

    public function validate($date, Constraint $constraint)
    {

        if ($date->format('N') == self::TUESDAY || $date->format('N') == self::SUNDAY) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}