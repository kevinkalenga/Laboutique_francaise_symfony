<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\OrderTypeForm;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;

final class OrderController extends AbstractController
{
     #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderTypeForm::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    } 
    
    


     /*
    * 2eme etape du tunel d'achat
    * Récap de la commande de l'utilisateur
    * Insertion en base de donnée en bdd, voila pk la fonction add
      Préparation du paiement vers Stripe
    */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request,  Cart $cart, EntityManagerInterface $entityManager): Response
    {
        
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        } 

        $products = $cart->getCart();
        
        $form = $this->createForm(OrderTypeForm::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
            
        ]);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()) {
                
            //  dd($form->getData());
            // stocker les informations dans la BDD
            
             // Création de la chaine d'addresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname() . ' ' . $addressObj->getLastname() . '<br/>';
            $address .= $addressObj->getAddress() . '<br/>';
            $address .= $addressObj->getPostal() . ' ' . $addressObj->getCity() . '<br/>';
            $address .= $addressObj->getCountry() . '<br/>';
            $address .= $addressObj->getPhone();
            
            

             $order = new Order();
             $order->setUser($this->getUser());
             $order->setCreatedAt(new \DateTime());
             $order->setState(1);
             $order->setCarrierName($form->get('carriers')->getData()->getName());
             $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
             $order->setDelivery($address); 

                // Création d'un nouvel objet orderDetail
                foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();
        }

        
        return $this->render('order/summary.html.twig', [
             'choices' => $form->getData(),
             'cart' => $products,
             'order' => $order,
             'totalWt' => $cart->getTotalWt()
        ]);
    }

}
