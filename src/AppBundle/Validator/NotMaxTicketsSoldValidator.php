<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 05/02/2018
 * Time: 14:12
 */

namespace AppBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotMaxTicketsSoldValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($protocol, Constraint $constraint)
    {

        $ticketsAvailable = $this->em->getRepository('AppBundle:Order')->countAvailableTickets($protocol->getDateOfVisit());

        if (($ticketsAvailable - $protocol->getNbTickets()) < 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}