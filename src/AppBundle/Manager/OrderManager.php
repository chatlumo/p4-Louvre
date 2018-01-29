<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 20/01/2018
 * Time: 11:52
 */

namespace AppBundle\Manager;

use AppBundle\Entity\Order;
use AppBundle\Entity\Ticket;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Exception\OrderManagerException;


class OrderManager
{
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;

    }

    public function initOrder() {
        $order = $this->getSessionOrder();

        if ( is_null($order) ) {
            $order = new Order();
        }

        return $order;
    }

    public function getOrder() {
        $order = $this->getSessionOrder();

        if ( is_null($order) ) {
            throw new OrderManagerException('app.fill.form1.first');
        }

        return $order;
    }

    public function setOrder(Order $order) {
        $this->setSessionOrder($order);
    }

    public function prepareOrder(Order $order) {
        //Creating empty tickets while number of tickets is less than number of tickets chosen
        while (count($order->getTickets()) < $order->getNbTickets()) {
            $ticket = new Ticket();
            $order->addTicket($ticket);
        }

        // Delete last X tickets if there are too much (user selects X fewer tickets)
        while (count($order->getTickets()) > $order->getNbTickets()) {
            $ticket = $order->getTickets()->last();
            $order->removeTicket($ticket);
        }

        $this->setSessionOrder($order);
    }

    private function getSessionOrder() {
        if ($this->session->has('order')) {
            if ($this->session->get('order') instanceof Order) {
                return $this->session->get('order');
            }
        }

        return null;
    }

    private function setSessionOrder(Order $order) {
        $this->session->set('order', $order);

    }

}