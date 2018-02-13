<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 04/02/2018
 * Time: 12:36
 */

namespace AppBundle\Service;


use AppBundle\Entity\Order;

class EmailSender
{
    private $templating;
    private $mailer;
    private $from_email;

    public function __construct(\Twig_Environment $templating, \Swift_Mailer $mailer, $from_email) {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->from_email = $from_email;
    }

    /**
     * @param $subject
     * @param Order $order
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendEmail($subject, Order $order) {
        $template = 'Email/success.html.twig';

        $message = new \Swift_Message($subject);
        $message
            ->setFrom($this->from_email)
            ->setTo($order->getEmail())
            ->setBody(
                $this->templating->render(
                    $template,
                    array(
                        'order' => $order
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);

    }




}