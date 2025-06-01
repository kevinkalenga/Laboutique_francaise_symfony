<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderDetailController extends AbstractController
{
    // Route personnalisée qui prend l'ID d'une commande et l'affiche
    #[Route('/admin/order/{id}', name: 'admin_order_detail')]
    public function index(Order $order): Response
    {
       // Affiche la vue Twig avec la commande passée comme variable
        return $this->render('admin/order.html.twig', [
            'order' => $order,
        ]);
    }
}
