<?php

namespace AppBundle\Controller;

use AppBundle\Manager\OrderManager;
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
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step1Action(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        //Step 1 : order informations
        $order = $orderManager->initOrder();

        $form1 = $this->createForm(OrderType::class, $order);
        $form1->handleRequest($request);

        // If form datas OK, go to Step 2
        if ($form1->isSubmitted() && $form1->isValid()) {

            $orderManager->prepareOrder($order);

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
    public function step2Action(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        //Step 2 : visitor informations
        if ($orderManager->hasOrder()) {
        //if ($session->has('order')) {
            $order = $orderManager->getOrder();

            $form2 = $this->createForm(TicketsFormType::class, $order);
            $form2->handleRequest($request);

            // Step 3 : verifying & pay
            if ($form2->isSubmitted() && $form2->isValid()) {


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

        $this->addFlash('error', 'Veuillez d\'abord remplir ce formulaire');
        return $this->redirectToRoute('step1');

    }
}
