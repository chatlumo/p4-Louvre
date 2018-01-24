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
    public $message = "Le billet journée n'est plus disponible pour le jour même";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}