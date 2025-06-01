<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/commande/paiement', name: 'app_payment')]
    public function index(): Response
    {
        Stripe::setApikey($_ENV['STRIPE_SECRET_KEY']);
        $YOUR_DOMAIN = 'https://127.0.0.1:8000';

        $checkout_session = Session::create([
            'line_items' => [[
              # Provide the exact Price ID (e.g. price_1234) of the product you want to sell
              'price_data' => [
                  'currency' => 'eur',
                  'unit_amount' => '1500',
                  'product_data' => [
                    'name' => 'Produit de test'
                  ]
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

       

        return $this->redirect($checkout_session->url);
        die('payment succed');
    }
}
