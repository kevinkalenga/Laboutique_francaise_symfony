<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;

final class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhait', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig');
    }
    
    #[Route('/compte/liste-de-souhait/add/{id}', name: 'app_account_wishlist_add')]
    public function add(ProductRepository $productRepository, $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Recup l'objet du produit souhaité 
        $product = $productRepository->findOneById($id);
        
        // Si le produit existe on l'ajoute à la wishlist 
        if($product) {
           $this->getUser()->addWishlist($product); 
            $entityManager->flush();
        }

       

        $this->addFlash(
            type: 'success',
            message: "Produit correctement ajouté à votre liste de souhait."
        );

       

         return $this->redirect($request->headers->get('referer'));
    }
    
    #[Route('/compte/liste-de-souhait/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove(ProductRepository $productRepository, $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Recup l'objet du produit à supprimer 
        $product = $productRepository->findOneById($id);
        
        // Si le produit existe, supprime le produit de la wishlist 
        if($product) {
           $this->addFlash('success', 'Produit correctement supprimé de votre liste de souhait');
           $this->getUser()->removeWishlist($product); 

           $entityManager->flush();
        } else {
           $this->addFlash('danger', 'Produit introuvable.');
        }

         return $this->redirect($request->headers->get('referer'));
    }
}
