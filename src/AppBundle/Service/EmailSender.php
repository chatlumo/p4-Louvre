<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 04/02/2018
 * Time: 12:36
 */

namespace AppBundle\Service;


class EmailSender
{
    private $templating;
    private $mailer;
    private $from_email;

    public function __construct(\Twig_Environment $templating, \Swift_Mailer $mailer, string $from_email) {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->from_email = $from_email;
    }

    /**
     * @param $to
     * @param $subject
     * @param $object
     * @param $template
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendEmail($to, $subject, $object, $template) {
        $message = (new \Swift_Message($subject))
            ->setFrom($this->from_email)
            ->setTo($to)
            ->setBody(
                $this->templating->render(
                    $template,
                    array('object' => $object)
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);

    }




}