<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 05/02/2018
 * Time: 14:11
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotClosingDay extends Constraint
{
    public $message = "app.dayOfVisit.is.closing.day";
}