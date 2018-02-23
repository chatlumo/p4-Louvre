<?php

namespace Tests\AppBundle\Service;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Order;
use AppBundle\Entity\Ticket;
use AppBundle\Service\PriceCalculator;

class PriceCalculatorTest extends TestCase
{
    const TUESDAY = 2;
    const SUNDAY = 7;

    /** @var \DateTime $dateOfVisit */
    protected $dateOfVisit;

    protected function setUp()
    {

        $today = new \DateTime();
        $daysLater = 14;

        if ($today->format('N') == self::TUESDAY || $today->format('N') == self::SUNDAY) {
            $daysLater++;
        }

        $interval = new \DateInterval('P'.$daysLater.'D');

        $this->dateOfVisit = $today->add($interval);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSeniorPrice($age, $duration, $reducedPrice, $expected){
        $interval = new \DateInterval('P'.$age.'Y');
        $birthdate = clone $this->dateOfVisit;
        $birthdate = $birthdate->sub($interval);

        //echo $birthdate->format('Y-m-d');

        $order = new Order();
        $order->setDateOfVisit($this->dateOfVisit);
        $order->setFullDay($duration);
        $order->setNbTickets(1);

        $ticket = new Ticket();
        $ticket->setBirthdate($birthdate);
        $ticket->setReducedPrice($reducedPrice);

        $ticket->setOrder($order);
        $order->addTicket($ticket);

        $priceCalculator = new PriceCalculator();

        $order = $priceCalculator->computePrice($order);

        $tickets = $order->getTickets();

        foreach ($tickets as $ticket) {
            $price = $ticket->getPrice();
        }


        $this->assertEquals($expected, $price);
    }

    public function dataProvider()
    {
        return [
            [3, Order::ORDER_FULL_DAY, Ticket::REDUCED_PRICE, 0],
            [3, Order::ORDER_FULL_DAY, Ticket::NO_REDUCED_PRICE, 0],
            [3, Order::ORDER_HALF_DAY, Ticket::REDUCED_PRICE, 0],
            [3, Order::ORDER_HALF_DAY, Ticket::NO_REDUCED_PRICE, 0],
            [6, Order::ORDER_FULL_DAY, Ticket::REDUCED_PRICE, 8],
            [6, Order::ORDER_FULL_DAY, Ticket::NO_REDUCED_PRICE, 8],
            [6, Order::ORDER_HALF_DAY, Ticket::REDUCED_PRICE, 4],
            [6, Order::ORDER_HALF_DAY, Ticket::NO_REDUCED_PRICE, 4],
            [15, Order::ORDER_FULL_DAY, Ticket::REDUCED_PRICE, 10],
            [15, Order::ORDER_FULL_DAY, Ticket::NO_REDUCED_PRICE, 16],
            [15, Order::ORDER_HALF_DAY, Ticket::REDUCED_PRICE, 5],
            [15, Order::ORDER_HALF_DAY, Ticket::NO_REDUCED_PRICE, 8],
            [63, Order::ORDER_FULL_DAY, Ticket::REDUCED_PRICE, 10],
            [63, Order::ORDER_FULL_DAY, Ticket::NO_REDUCED_PRICE, 12],
            [63, Order::ORDER_HALF_DAY, Ticket::REDUCED_PRICE, 5],
            [63, Order::ORDER_HALF_DAY, Ticket::NO_REDUCED_PRICE, 6],
        ];
    }

}