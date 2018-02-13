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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Exception\OrderManagerException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Service\EmailSender;


class OrderManager
{
    private $session;
    private $em;
    private $validator;
    private $emailSender;

    /**
     * OrderManager constructor.
     * @param SessionInterface $session
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param \Twig_Environment $templating
     * @param \Swift_Mailer $mailer
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $em, ValidatorInterface $validator, EmailSender $emailSender) {
        $this->session = $session;
        $this->em = $em;
        $this->validator = $validator;
        $this->emailSender = $emailSender;
    }

    /**
     * @return Order
     */
    public function initOrder() {
        $order = $this->getSessionOrder();

        if ( !$order ) {
            $order = new Order();
        }

        return $order;
    }

    /**
     * @return Order|null
     */
    public function getOrder() {
        $order = $this->getSessionOrder();

        if ( !$order ) {
            throw new OrderManagerException('app.fill.form1.first');
        }

        return $order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order) {
        $this->setSessionOrder($order);
    }

    /**
     * @param Order $order
     */
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

    /**
     * @param Order $order
     * @param $transId
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function completeOrder(Order $order, $transId) {
        //Set transId
        $order->setTransId($transId);
        //Set orderDate
        $order->setOrderDate(new \DateTime('now'));
        //Set reference
        $order->setReference(strtoupper(uniqid()));


        $errors = $this->validator->validate($order);

        if (count($errors) > 0) {
            $errMsg = '';
            foreach ($errors as $error) {
                $errMsg .= $error->getMessage().' ';
            }

            throw new OrderManagerException($errMsg);
        }

        $this->em->persist($order);
        $this->em->flush();


        //send email to customer
        $this->emailSender->sendEmail( 'Hello Email', $order);


        $this->setSessionOrder($order);
    }

    /**
     * @return mixed|null
     */
    private function getSessionOrder() {
        if ($this->session->has('order')) {
            if ($this->session->get('order') instanceof Order) {
                return $this->session->get('order');
            }
        }

        return null;
    }

    /**
     * @param Order $order
     */
    private function setSessionOrder(Order $order) {
        $this->session->set('order', $order);

    }

}