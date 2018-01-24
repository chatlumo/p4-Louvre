<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 20/01/2018
 * Time: 15:58
 */

namespace AppBundle\Service;

use AppBundle\Entity\Order;


class PriceCalculator
{
    // age range in years
    const BABY_AGE = 4;
    const CHILD_AGE = 12;
    const SENIOR_AGE = 60;

    // prices in €
    const BABY_PRICE = 0;
    const CHILD_PRICE = 8;
    const SENIOR_PRICE = 12;
    const NORMAL_PRICE = 16;
    const REDUCED_PRICE = 10;

    // duration of visit
    const FULL_DAY = 1;
    const HALF_DAY = 0.5;

    private $visitDuration;

    public function getPrice(Order $order) {

        if ($order->getFullDay()) {
            $this->visitDuration = self::FULL_DAY;
        } else {
            $this->visitDuration = self::HALF_DAY;
        }

        $tickets = $order->getTickets();
        $dateOfVisit = $order->getDateOfVisit();
        $totalPrice = 0;

        foreach ($tickets as $ticket) {
            $price = 0;
            $age = $ticket->getBirthdate()->diff($dateOfVisit)->format('%y');

            switch ($age) {
                case ($age < self::BABY_AGE):
                    $price = self::BABY_PRICE;
                    break;
                case ($age < self::CHILD_AGE):
                    $price = self::CHILD_PRICE;
                    break;
                case ($age > self::SENIOR_AGE):
                    $price = self::SENIOR_PRICE;
                    break;
                default:
                    $price = self::NORMAL_PRICE;
            }

            if ($ticket->getReducedPrice()) {
                $price = self::REDUCED_PRICE;
            }

            $price = $price * $this->visitDuration;

            $ticket->setPrice($price);

            $totalPrice += $price;
        }

        return $order;

    }

}