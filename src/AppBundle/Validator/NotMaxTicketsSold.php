<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotMaxTicketsSold extends Constraint
{
    public $message = "app.dayOfVisit.max.tickets.sold";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}