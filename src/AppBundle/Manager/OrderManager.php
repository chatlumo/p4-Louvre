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


class OrderManager
{
    private $session;
    private $order;

    public function __construct(SessionInterface $session) {
        $this->session = $session;

    }

    public function initOrder() {
        if ( is_null($this->order) ) {
            $this->order = new Order();
        }

        //$this->getSessionOrder();

        return $this->getOrder();

    }

    public function getOrder() {
        $this->getSessionOrder();

        return $this->order;

    }

    public function setOrder(Order $order) {
        $this->order = $order;

        return $this;
    }

    public function hasOrder() {
        return !is_null($this->getOrder());

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
        $this->order = $order;

        return;

    }

    public function getSessionOrder() {
        if ($this->session->has('order')) {
            if ($this->session->get('order') instanceof Order) {
                $this->order = $this->session->get('order');
            }
        }

        return $this;

    }

    public function setSessionOrder(Order $order) {
        $this->session->set('order', $order);

        return $this;

    }

}