<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

final class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        // le slug va permettre d'aller chercher la categorie qui porte le mm nom ds la bdd

        // Une repository est une class permettant de faire des requettes dans une table dediée de la bdd
        $category = $categoryRepository->findOneBySlug($slug);

        if (!$category) {
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('category/index.html.twig', [
              'category' => $category,
        ]);
    }
}
