<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[IsGranted('ROLE_USER')]
final class SubscriptionController extends AbstractController
{
    private Subscription $subscription;
    public function __construct(private EntityManagerInterface $em)
    {
        $this->subscription = $this->getUser()->getSubscription();
    }

    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): RedirectResponse
    {
        try {

            // dd($subscription);
            if ($this->subscription == null || !$this->subscription->isActive()) {
                $checkoutUrl = $ps->setPayment(
                    $this->getUser(),
                    intval($request->get('plan'))
                );
                return $this->redirectToRoute('app_subscription_check', ['link' => $checkoutUrl]);
            }

            $this->addFlash('warning', "Vous êtes déjà abonné(e)");
            return $this->redirectToRoute('app_profile');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la redirection vers le paiement');
            return $this->redirectToRoute('app_profile');
        }
    }

    #[Route('/subscription/check', name: 'app_subscription_check', methods: ['GET'])]
    public function check(Request $request): Response
    {
        // Logique de traitement du succès
        return $this->render('subscription/check.html.twig', [
            'link' => $request->get('link'),
        ]);
    }

    #[Route('/subscription/success', name: 'app_subscription_success', methods: ['GET'])]
    public function subsSucces(): Response
    {
        $this->subscription->setIsActive(true)
                    ->setUpdatedAtValue();
        $this->em->persist($this->subscription);
        $this->em->flush();
        $this->addFlash('success', 'Vous êtes désormais abonné(e)');
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/subscription/cancel', name: 'app_subscription_cancel', methods: ['GET'])]
    public function subsCancel(): Response
    {
        $this->em->remove($this->subscription);
        $this->em->flush();
        $this->addFlash('warning', "Vous avez abandonné(e) votre tentative d'abonnement");
        return $this->redirectToRoute('app_profile');
    }
}
