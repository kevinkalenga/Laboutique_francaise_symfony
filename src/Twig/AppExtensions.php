<?php

namespace App\Twig;




use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;


class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        
    }

    // Création de filtre pour le price
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice'])
        ];
    }

    public function formatPrice($number)
    {
        return number_format($number, '2', ',') . ' €';
    }
    
    // fonction permettant d afficher toutes les categories
    public function getGlobals(): array
    {
        // creation des variables globals qu'on peux utiliser partout
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            

        ];
    }

    
}
