<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 21/01/2018
 * Time: 14:45
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AllowFullDay extends Constraint
{
    public $message = "app.full_day.not_available";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}