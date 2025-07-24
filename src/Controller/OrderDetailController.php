<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class OrderDetailController extends AbstractController
{
    #[Route('/admin/order/{id}', name: 'admin_order_detail')]
    public function index(Order $order, OrderRepository $orderRepository): Response
    {
        // Préparer les URLs vers les listes de commandes par état
        $urlsByState = [
            'preparation' => $this->generateUrl('admin_orders_by_state', ['state' => 'preparation']),
            'expediee'   => $this->generateUrl('admin_orders_by_state', ['state' => 'expediee']),
            'annulee'    => $this->generateUrl('admin_orders_by_state', ['state' => 'annulee']),
        ];

        return $this->render('admin/order.html.twig', [
            'order'       => $order,
            'urlsByState' => $urlsByState,
        ]);
    }

    
     // Liste des commandes filtrées par état (ex: préparation, expédiée, annulée)
    #[Route('/admin/orders/state/{state}', name: 'admin_orders_by_state')]
    public function ordersByState(string $state, OrderRepository $orderRepository): Response
    {
        $stateCode = $this->convertStateNameToCode($state);

       $orders = $orderRepository->findOrdersByStateCode($stateCode);


        return $this->render('admin/orders_by_state.html.twig', [
            'orders' => $orders,
            'state' => $state,
        ]);
    }

    /**
     * Convertit le nom d'état en code interne (exemple)
     */
    private function convertStateNameToCode(string $stateName): int
    {
    //     switch ($stateName) {
    //     case 'preparation':
    //         return 3;    // En cours de préparation
    //     case 'expediee':
    //         return 4;    // Expédiée
    //     case 'annulee':
    //         return 5;    // Annulée
    //     case 'payee':
    //         return 2;    // Payée (au cas où tu veux filtrer ça)
    //     case 'enattente':
    //         return 1;    // En attente de paiement
    //     default:
    //         return 0;    // Aucun état reconnu
    // }

       return match ($stateName) {
        'preparation' => 3,
        'expediee'    => 4,
        'annulee'     => 5,
        'payee'       => 2,
        'enattente'   => 1,
        default       => throw new \InvalidArgumentException('État inconnu : ' . $stateName),
    };
    }
}
