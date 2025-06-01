<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\HttpFoundation\Response;

class OrderCrudController extends AbstractCrudController
{
    // Spécifie à EasyAdmin que ce CRUD gère l'entité Order
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    
    // Personnalisation du CRUD (libellés, affichage...)
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->showEntityActionsInlined();  // Affiche les actions à droite des lignes
    }

    public function configureActions(Actions $actions): Actions
    {
    // Crée une action "Afficher" liée à la méthode show() et à une entité
       // Création d'une action personnalisée qui redirige vers une route Symfony
           $show = Action::new('Afficher', 'Afficher')
        ->linkToRoute('admin_order_detail', function (Order $order): array {
            return ['id' => $order->getId()];  // Passe l'ID à la route
        })
        ->setIcon('fa fa-eye')  // Icône font-awesome
        ->addCssClass('btn btn-info');  // Classe Bootstrap pour le style

    return $actions
        ->add(Crud::PAGE_INDEX, $show) // Ajout de l'action à la liste
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_INDEX, Action::DELETE);
    } 


    public function show(AdminContext $context): Response
{
    $entityDto = $context->getEntity();

    if ($entityDto === null) {
        throw new \RuntimeException('Aucune entité trouvée dans ce contexte.');
    }

    $order = $entityDto->getInstance();

    // Pour debug
    // dd($order);

    return $this->render('admin/order.html.twig', [
        'order' => $order,
    ]);
}



    
     // Définition des champs affichés dans le CRUD
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')->setLabel('Date'),
            NumberField::new('state')->setLabel('Status')->setTemplatePath('admin/state.html.twig'),
            AssociationField::new('user')->setLabel('Utilisateur'),
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('totalTva')->setLabel('Total TVA'),
            NumberField::new('totalWt')->setLabel('Total TTC'),
        ];
    }
}



// namespace App\Controller\Admin;

// use App\Entity\Order;
// use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
// use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
// use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
// use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
// use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
// use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
// use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
// use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
// use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
// use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
// use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

// class OrderCrudController extends AbstractCrudController
// {
//     public static function getEntityFqcn(): string
//     {
//         return Order::class;
//     }

//     public function configureCrud(Crud $crud): Crud
//     {
//         return $crud
//             ->setEntityLabelInSingular('Commande')
//             ->setEntityLabelInPlural('Commandes');

//         // ...

//     }

//     public function configureActions(Actions $actions): Actions
//     {
//          $show = Action::new('Afficher')->linkToCrudAction('show');
        
//         // suppression des actions ds easyAdmin
//         return $actions
//             ->add(Crud::PAGE_INDEX,  $show)
//             ->remove(Crud::PAGE_INDEX, Action::NEW)
//             ->remove(Crud::PAGE_INDEX, Action::EDIT)
//             ->remove(Crud::PAGE_INDEX, Action::DELETE);
//     }

//     public function show(AdminContext $context)
//     {
//          $order = $context->getEntity()->getInstance();

//          return $this->render('admin/order.html.twig', [
//             'order' => $order
//         ]);

        
//     }

//     public function configureFields(string $pageName): iterable
//     {
//         return [

//             IdField::new('id'),
//             DateField::new('createdAt')->setLabel('Date'),
//             NumberField::new('state')->setLabel('Status')->setTemplatePath('admin/state.html.twig'),
//             AssociationField::new('user')->setLabel('Utilisateur'),
//             TextField::new('carrierName')->setLabel('Transporteur'),
//             NumberField::new('totalTva')->setLabel('Total TVA'),
//             NumberField::new('totalWt')->setLabel('Total TTC')
//         ];
//     }


//     /*
//     public function configureFields(string $pageName): iterable
//     {
//         return [
//             IdField::new('id'),
//             TextField::new('title'),
//             TextEditorField::new('description'),
//         ];
//     }
//     */
// }




