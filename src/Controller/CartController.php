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
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
             'cart' => $cart->getCart()
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

        return $this->redirectToRoute('app_product', [
            'slug' => $product->getSlug()
        ]);

        // return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {

        $cart->remove();



        return $this->redirectToRoute('app_home');
    }

}
