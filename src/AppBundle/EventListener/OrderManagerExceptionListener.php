<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 29/01/2018
 * Time: 12:00
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use AppBundle\Exception\OrderManagerException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class OrderManagerExceptionListener
{
    private $router;
    private $session;

    public function __construct(Router $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        if (!$exception instanceof OrderManagerException) {
            return;
        }
        $message = $exception->getMessage();


        $this->session->getFlashBag()->add('notice', $message);

        $url = $this->router->generate('step1');
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
}