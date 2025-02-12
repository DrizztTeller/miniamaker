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

class PaymentService
{
    public function __construct(
        private ParameterBagInterface $params,
        private SubscriptionRepository $sr,
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
    ) {}

    public function setPayment(User $user, int $amount): string
    {
        Stripe::setApiKey($this->params->get('STRIPE_SK'));

        $subscription = new Subscription();
        $subscription
            ->setClient($user)
            ->setAmount($amount)
            ->setFrequency($amount > 99 ? 'year' : 'month')
        ;

        try {
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
                            'name' => 'Abonnement miniamaker', // Texte affiché sur la page de paiement
                        ],
                    ],
                    'quantity' => 1, // Qt obligatoire
                ]],
                'mode' => 'subscription', // Mode de paiement
                // Redireection après le paiement (réussi ou échoué)
                'success_url' => $this->params->get('APP_URL') . '/subscription/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->params->get('APP_URL') . '/subscription/cancel',
            ]);
            return $checkout_session->url ?? 'TEST'; // Le service retourne une URL au controleur
        } catch (\Throwable $th) {
            echo $th->getMessage() . PHP_EOL;
            echo json_encode(['error' => $th->getMessage()]);
            // return $checkout_session->cancel_url ?? 'CANCEL';
        }
    }
}