<?php

namespace App\Service;


use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Subscription;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentService
{
    public function __construct(
        private ParameterBagInterface $params,
        private SubscriptionRepository $sr,
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function setPayment(User $user, int $amount): string
    {
        Stripe::setApiKey($this->params->get('STRIPE_SK'));

        
        try {
            $subscription = $user->getSubscription();
            
            if ($user->getSubscription() === null) {
                $subscription = new Subscription();
                $subscription->setClient($user);
            }
    
            $subscription
                ->setAmount($amount)
                ->setFrequency($amount > 99 ? 'year' : 'month')
            ;

            $this->em->persist($subscription);
            $this->em->flush();

            $checkout_session = Session::create([
                'payment_method_types' => ['card'], // Setup du moyen de paiement
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur', // Setup de la devise
                        'unit_amount' => $amount * 100, // Montant en centimes
                        'recurring' => [ // Recurrence de l'abonnement
                            'interval' => $subscription->getFrequency(), // mois ou année
                        ],
                        'product_data' => [ // Informations du produit
                            'name' => 'miniamaker', // Texte affiché sur la page de paiement
                        ],
                    ],
                    'quantity' => 1, // Qt obligatoire
                ]],
                'mode' => 'subscription', // Mode de paiement
                // Redirection après le paiement (réussi ou échoué)
                'success_url' => $this->urlGenerator->generate('app_subscription_success', 
                    ['session_id' => '{CHECKOUT_SESSION_ID}'], 
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'cancel_url' => $this->urlGenerator->generate('app_subscription_cancel', 
                    [], 
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]);

            if (!isset($checkout_session->url)) {
                throw new \Exception('Erreur lors de la création de la session de paiement');
            }

            return $checkout_session->url; // Le service retourne une URL au controleur
        } catch (\Throwable $th) {
            throw new \RuntimeException('Erreur lors de la création de la session de paiement : ' . $th->getMessage());
        }
    }
}
