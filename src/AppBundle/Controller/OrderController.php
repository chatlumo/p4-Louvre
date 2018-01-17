<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Order;
use AppBundle\Entity\Ticket;
use AppBundle\Form\OrderType;
use AppBundle\Form\TicketsFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class OrderController
 * @package AppBundle\Controller
 */
class OrderController extends Controller
{

    /**
     * @Route("/", name="step1")
     * @param Request $request
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step1Action(Request $request, SessionInterface $session)
    {
        //Step 1 : order informations
        $order = new Order();

        if ($session->has('order')) {
            $order = $session->get('order');
        }

        $form1 = $this->createForm(OrderType::class, $order);
        $form1->handleRequest($request);

        // If datas OK, go to Step 2
        if ($form1->isSubmitted() && $form1->isValid()) {

            $session->set('order', $order);

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

            return $this->redirectToRoute('step2');
        }

        return $this->render('default/index.html.twig', array(
            'test' => 'truc',
            'form' => $form1->createView(),
        ));
    }

    /**
     * @Route("/step2", name="step2")
     */
    public function step2Action(Request $request, SessionInterface $session)
    {
        //Step 2 : visitor informations
        if ($session->has('order')) {
            $order = $session->get('order');

            $form2 = $this->createForm(TicketsFormType::class, $order);
            $form2->handleRequest($request);

            // Step 3 : verifying & pay
            if ($form2->isSubmitted() && $form2->isValid()) {

                //$order = $session->get('order');
                //$order->addTicket($ticket);
                //$session->set('order', $order);

                dump($order->getTickets());

                // Calcul du prix

                //return $this->redirectToRoute('step3');
                return $this->render('default/index.html.twig', array(
                    'test' => 'step 3',
                ));
            }

            // Step 2 : ticket details
            return $this->render('default/index.html.twig', array(
                'test' => 'step 2',
                'form' => $form2->createView(),
            ));
        }

        return $this->redirectToRoute('step1');

    }
}
