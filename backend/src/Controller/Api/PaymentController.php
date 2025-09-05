<?php

namespace App\Controller\Api;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/api/payments')]
class PaymentController extends AbstractController
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
        // Initialiser Stripe avec la clé secrète
        Stripe::setApiKey($this->parameterBag->get('stripe_secret_key'));
    }

    /**
     * Créer une session de paiement Stripe
     */
    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['amount']) || $data['amount'] <= 0) {
                return new JsonResponse(['error' => 'Montant invalide'], 400);
            }

            $session = $this->createStripeSession($data);
            
            return new JsonResponse([
                'sessionId' => $session->id,
                'url' => $session->url
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création de la session de paiement',
                'message' => $e->getMessage()
            ], 500);
        }
    }
/**
 * Crée une session Stripe Checkout
 *
 * @param array<string,mixed> $data
 * @return Session
 */
private function createStripeSession(array $data): Session
{
    $sessionData = $this->buildSessionData($data);
    return Session::create($sessionData);
}

    private function buildSessionData(array $data): array
    {
        $userId = $this->getUserId();
        $frontendUrl = $this->parameterBag->get('app_frontend_url');
        
        return [
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($data),
            'mode' => 'payment',
            'success_url' => $frontendUrl . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl . '/payment/cancel',
            'metadata' => [
                'user_id' => $userId,
                'donation_type' => $data['type'] ?? 'general'
            ]
        ];
    }


    private function getUserId(): string
    {
        $user = $this->getUser();
        return $user ? (string)$user->getId() : 'anonymous';
    }

    private function buildLineItems(array $data): array
    {
        return [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Don pour TaskForce',
                        'description' => 'Soutien au développement de TaskForce'
                    ],
                    'unit_amount' => $data['amount'] * 100,
                ],
                'quantity' => 1,
            ],
        ];
    }

    /**
     * Récupérer les détails d'une session de paiement
     */
    #[Route('/session/{sessionId}', name: 'get_session', methods: ['GET'])]
    public function getSession(string $sessionId): JsonResponse
    {
        try {
            $session = Session::retrieve($sessionId);
            
            return new JsonResponse([
                'id' => $session->id,
                'status' => $session->payment_status,
                'amount' => $session->amount_total,
                'currency' => $session->currency,
                'customer_email' => $session->customer_details->email ?? null,
                'created' => $session->created
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Session introuvable',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Webhook pour traiter les événements Stripe
     */
    #[Route('/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = $this->parameterBag->get('stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Payload invalide'], 400);
        } catch (SignatureVerificationException $e) {
            return new JsonResponse(['error' => 'Signature invalide'], 400);
        }

        // Traiter l'événement
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                // Logique pour traiter le paiement réussi
                // TODO: Sauvegarder en base de données
                break;
            
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // Logique pour traiter le paiement confirmé
                break;
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * Récupérer les statistiques de dons
     */
    #[Route('/stats', name: 'get_stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        try {
            // Pour l'instant, retourner des données de test
            // TODO: Implémenter avec le repository Payment
            return new JsonResponse([
                'totalDonations' => 0,
                'totalDonors' => 0,
                'thisMonth' => 0
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des statistiques',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
