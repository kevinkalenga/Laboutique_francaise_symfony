<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;


class OrderCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager
    ) {
         $this->urlGenerator = $urlGenerator;
         $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $markAsPreparation = Action::new('markAsPreparation', 'Passer en préparation')
            ->linkToCrudAction('markAsPreparation')
            ->setCssClass('btn btn-sm btn-info')
            ->setIcon('fa fa-box')
            ->displayIf(function (Order $order) {
            return $order->getState() === 2; // seulement si payée
        });
        
        $customDetail = Action::new('customDetail', 'Voir')
            ->linkToUrl(function (Order $order) {
                return $this->urlGenerator->generate('admin_order_detail', [
                    'id' => $order->getId()
                ]);
            })
            ->setIcon('fa fa-eye')
            ->setCssClass('btn btn-sm btn-outline-primary');
        // 🔹 Bouton vers les commandes en préparation
       $preparationOrders = Action::new('preparation', 'Préparation')
        ->linkToUrl(function () {
            return $this->urlGenerator->generate('admin_orders_by_state', [
                'state' => 'preparation'
            ]);
        })
        ->setIcon('fa fa-box')
        ->setCssClass('btn btn-sm btn-outline-info');
      
        $expedieeOrders = Action::new('expediee', 'Expédiée')
        ->linkToUrl(function () {
            return $this->urlGenerator->generate('admin_orders_by_state', ['state' => 'expediee']);
        })
        ->setIcon('fa fa-truck')
        ->setCssClass('btn btn-sm btn-outline-success');

       $annuleeOrders = Action::new('annulee', 'Annulée')
        ->linkToUrl(function () {
            return $this->urlGenerator->generate('admin_orders_by_state', ['state' => 'annulee']);
        })
        ->setIcon('fa fa-times')
        ->setCssClass('btn btn-sm btn-outline-danger');
        
        
        
        return $actions
            ->add(Crud::PAGE_INDEX, $markAsPreparation)
            ->add(Crud::PAGE_INDEX, $customDetail)
            ->add(Crud::PAGE_INDEX, $preparationOrders)
            ->add(Crud::PAGE_INDEX, $expedieeOrders)
            ->add(Crud::PAGE_INDEX, $annuleeOrders)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    
     
      public function markAsPreparation(AdminContext $context, Request $request): RedirectResponse
{
    $entityId = $request->query->get('entityId');

    if (!$entityId) {
        $this->addFlash('danger', 'Aucune commande sélectionnée.');
        return $this->redirect($this->generateUrl('admin'));
    }

    $order = $this->entityManager->getRepository(Order::class)->find($entityId);

    if (!$order) {
        $this->addFlash('danger', 'Commande introuvable.');
        return $this->redirect($this->generateUrl('admin'));
    }

    // **Ici, la vérification importante**
    if ($order->getState() === 5) {  // 5 = annulée
        $this->addFlash('warning', 'Cette commande est annulée et ne peut pas être modifiée.');
        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('admin'));
    }

    if ($order->getState() === 2) { // Si payée
        $order->setState(3);        // Passer en préparation
        $this->entityManager->flush();
        $this->addFlash('success', 'Commande passée en préparation.');
    } else {
        $this->addFlash('warning', 'La commande ne peut pas être mise en préparation.');
    }

    return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('admin'));
}



    
    
    
    
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')->setLabel('Date'),
            ChoiceField::new('state')
    ->setLabel('Statut')
    ->setChoices([
        'En attente de paiement' => 1,
        'Payée' => 2,
        'En cours de préparation' => 3,
        'Expédiée' => 4,
        'Annulée' => 5,
    ])
    ->renderAsBadges([
        1 => 'secondary',
        2 => 'success',
        3 => 'info',
        4 => 'warning',
        5 => 'danger',
    ]),
     

            
    // NumberField::new('state')->setLabel('Statut'),
            AssociationField::new('user')->setLabel('Utilisateur'),
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('totalTva')->setLabel('Total TVA'),
            NumberField::new('totalWt')->setLabel('Total TTC'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
             ->add(ChoiceFilter::new('state')->setLabel('Statut')->setChoices([
        'En attente de paiement' => 1,
        'Payée' => 2,
        'En cours de préparation' => 3,
        'Expédiée' => 4,
        'Annulée' => 5,
    ]));
    }
}
