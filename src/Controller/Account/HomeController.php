<?php

namespace App\Controller\Account;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OrderRepository;



final class HomeController extends AbstractController
{

    #[Route('/compte', name: 'app_account')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy([
            'user' => $this->getUser(),
            'state' => [2, 3]
        ]);
        
        return $this->render('account/index.html.twig', [
             'orders' => $orders
        ]);
    }
}
