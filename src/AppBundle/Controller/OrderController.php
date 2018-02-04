<?php

namespace AppBundle\Controller;

use AppBundle\Manager\OrderManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stripe\Charge;
use Stripe\Stripe;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step1Action(Request $request, OrderManager $orderManager)
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step2Action(Request $request, OrderManager $orderManager)
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
     * @param OrderManager $orderManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function step3Action(OrderManager $orderManager)
    {
        // Step 3 : Check before to pay
        $order = $orderManager->getOrder();

        return $this->render('default/step3.html.twig', array(
            'order' => $order,
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
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function checkoutAction(Request $request, OrderManager $orderManager)
    {
        $order = $orderManager->getOrder();

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        // Get the credit card details submitted by the form
        $token = $request->request->get('stripeToken');

        // Create a charge: this will charge the user's card
        try {
            $charge = Charge::create(array(
                "amount" => $order->getTotalAmount() * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Billetterie Musée du Louvre"
            ));

        } catch(\Exception $e) {

            $this->addFlash("error","Le paiement n'a pas fonctionné, veuillez réessayer.");
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
     * @param OrderManager $orderManager
     * @param SessionInterface $session
     * @return mixed
     */
    public function successAction(OrderManager $orderManager, SessionInterface $session)
    {
        $order = $orderManager->getOrder();
        $reference = $order->getReference();
        $session->clear();

        return $this->render('default/success.html.twig', array(
            'order' => $order,
            'reference' => $reference
        ));
    }
}
