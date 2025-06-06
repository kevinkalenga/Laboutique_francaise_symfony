<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Classe\Cart;

final class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: ['motif' => null])]
    public function index(Cart $cart, $motif): Response
    {
        
        if ($motif == "annulation") {
            $this->addFlash(
                type: 'info',
                message: 'Paiement annulé : Vous pouvez mettre à jour votre panier et votre commande.'
            );
        }
        
        
        return $this->render('cart/index.html.twig', [
             'cart' => $cart->getCart(),
             'totalWt' => $cart->getTotalWt()
        ]);
    }

    // La route permettant à ajouter un produit dans le panier
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->findOneById($id);
        $cart->add($product);

        $this->addFlash(
            type: 'success',
            message: "Produit correctement ajouté à votre panier."
        );

       

         return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {

        $cart->decrease($id);

        $this->addFlash(
            type: 'success',
            message: "Produit correctement supprimée de votre panier."
        );

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {

        $cart->remove();



        return $this->redirectToRoute('app_home');
    }

}
