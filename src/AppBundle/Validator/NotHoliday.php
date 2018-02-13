<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotHoliday extends Constraint
{
    public $message = "app.dayOfVisit.is.holiday";
}