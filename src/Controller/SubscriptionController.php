<?php

namespace App\Controller;

use App\Service\PaymentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class SubscriptionController extends AbstractController {

    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): Response
    {
        $subsUser = $this->getUser()->getSubscription();
        // dd($request->get('plan'));

        if ($subsUser == null || $subsUser->isActive() === false) {
            $checkoutUrl = $ps->setPayment(
                $this->getUser(),
                intval($request->get('plan'))
            );

            // dd($checkoutUrl);
            return $this->redirect($checkoutUrl);
        } else {
            $this->addFlash('warning', 'Vous avez déjà abonné(e)');
            return $this->redirectToRoute('app_profile');
        }
    }
    #[Route('/subscription/success', name: 'app_subscription_success', methods: ['GET'])]
    public function subsSucces(): Response
    {
            $this->addFlash('success', 'Vous êtes désormais abonné');
            return $this->redirectToRoute('app_profile');
    }

    #[Route('/subscription/cancel', name: 'app_subscription_cancel', methods: ['GET'])]
    public function subsCancel(): Response
    {
            $this->addFlash('warning', "Vous avez abandonné(e) votre tentative d'abonnement");
            return $this->redirectToRoute('app_profile');
    }
}
