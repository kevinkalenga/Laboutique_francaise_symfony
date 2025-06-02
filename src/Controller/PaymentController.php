<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository): Response
    {
        Stripe::setApikey($_ENV['STRIPE_SECRET_KEY']);
       

         $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
    ]);

    if (!$order) {
      return $this->redirectToRoute('app_home');
    }
        
         $products_for_stripe = [];
    foreach ($order->getOrderDetails() as $product) {
      $products_for_stripe[] = [
        'price_data' => [
          'currency' => 'eur',
          'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, '', ''),
          'product_data' => [
            'name' => $product->getProductName(),
            'images' => [
               $_ENV['DOMAIN'] . '/uploads/' . $product->getProductIllustration()
            ]
          ]
        ],
        'quantity' => $product->getProductQuantity(),
      ];
    }
        
     $products_for_stripe[] = [
      'price_data' => [
        'currency' => 'eur',
        'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
        'product_data' => [
          'name' => 'Transporteur: ' . $order->getCarrierName(),
        ]
      ],
      'quantity' => 1,
    ];   
        
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [[
              # Provide the exact Price ID (e.g. price_1234) of the product you want to sell
              $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' =>  $_ENV['DOMAIN'] . '/success.html',
            'cancel_url' =>  $_ENV['DOMAIN'] . '/cancel.html',
        ]);

       

        return $this->redirect($checkout_session->url);
       
    }
}
