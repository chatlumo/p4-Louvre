<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 21/01/2018
 * Time: 14:14
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllowFullDayValidator extends ConstraintValidator
{

    const LIMIT_HOUR = 14;

    public function validate($protocol, Constraint $constraint)
    {
        $now = new \DateTime();
        $today = new \DateTime('today');
        $dateOfVisit = $protocol->getDateOfVisit();

        $nowHour = $now->format('H');

        $sameDay = ($dateOfVisit->diff($today)->format('%a')) == 0 ? true : false;


        if (($protocol->getFullDay() === true) && $sameDay && $nowHour > self::LIMIT_HOUR) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}