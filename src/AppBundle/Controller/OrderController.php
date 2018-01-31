<?php

namespace AppBundle\Controller;

use AppBundle\Manager\OrderManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
            'btnSubmit' => "Passer à l'étape 2",
            'form' => $form1->createView(),
        ));
    }

    /**
     * @Route("/step2", name="step2")
     * @param Request $request
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step2Action(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        //Step 2 : visitor informations
        $order = $orderManager->getOrder();

        $form2 = $this->createForm(TicketsFormType::class, $order);
        $form2->handleRequest($request);

        // Step 3 : verifying & pay
        if ($form2->isSubmitted() && $form2->isValid()) {

            $this->get('priceCalculator')->computePrice($order);

            return $this->redirectToRoute('step3');
        }

        // Step 2 : ticket details
        return $this->render('default/index.html.twig', array(
            'btnSubmit' => "Passer à l'étape 3",
            'form' => $form2->createView(),
        ));

    }

    /**
     * @Route("/step3", name="step3")
     * @param Request $request
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function step3Action(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        // Step 3 : Check before to pay
        $order = $orderManager->getOrder();

        return $this->render('default/step3.html.twig', array(
            'order' => $order,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ));

    }

    /**
     * @Route(
     *     "/checkout",
     *     name="checkout",
     *     methods="POST"
     * )
     * @param Request $request
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return mixed
     */
    public function checkoutAction(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        $order = $orderManager->getOrder();

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        // Get the credit card details submitted by the form
        $token = $request->request->get('stripeToken');

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $order->getTotalAmount() * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Billetterie Musée du Louvre"
            ));

        } catch(\Stripe\Error\Card $e) {

            $this->addFlash("error","Le paiement n\'a pas fonctionné, veuillez réessayer.");
            return $this->redirectToRoute("step3");
            // The card has been declined
        }

        // update and persist order
        $orderManager->completeOrder($order, $charge->id);

        return $this->redirectToRoute("success");
    }

    /**
     * @Route(
     *     "/success",
     *     name="success"
     * )
     * @param Request $request
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return mixed
     */
    public function successAction(Request $request, OrderManager $orderManager, SessionInterface $session)
    {
        $order = $orderManager->getOrder();
        dump($order);
        /*
        $validator = $this->get('validator');
        $errors = $validator->validate($order);
        dump(count($errors));
        */

        /*

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $order->getTotalAmount() * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Billetterie Musée du Louvre"
            ));
            //$charge->id;
            return $this->redirectToRoute("success");
        } catch(\Stripe\Error\Card $e) {

            $this->addFlash("error","Le paiement n\'a pas fonctionné, veuillez réessayer.");
            return $this->redirectToRoute("step3");
            // The card has been declined
        }
        */
        return $this->render('default/success.html.twig', array(
            'order' => $order,
        ));
    }
}
