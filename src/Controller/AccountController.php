<?php

namespace App\Controller;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AddressRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PasswordUserForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AddressUserTypeForm;

final class AccountController extends AbstractController
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    
    
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        // on crée un formumaire et on lui passe une option passwordHasher qui verif le mdp
        $form = $this->createForm(PasswordUserForm::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        // Ecoute la requette

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash(
                type: 'success',
                message: "Votre mot de passe est correctement mis à jour ."
            );
            
        }
       
        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    } 

    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function addresses(): Response
    {
        return $this->render('account/addresses.html.twig');
    }
    
    
     #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function deleteDelete($id, AddressRepository $adressRepository): Response
    {
        $address = $adressRepository->findOneById($id);
        if (!$address or $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_addresses');
        }

        $this->addFlash(
            type: 'success',
            message: "Votre addresse est correctement supprimée."
        );
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_account_addresses');
    }
    
    
    
    
    
    
    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function addressForm(Request $request, $id, AddressRepository $adressRepository): Response
    {
        // par defaut id est null est on entre ds le else mais lors de l'edition il est à true
        if ($id) {
            $address = $adressRepository->findOneById($id);
            if (!$address or $address->getUser() != $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }
        
        
        $form = $this-> createForm(AddressUserTypeForm::class, $address);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();

             $this->addFlash(
                type: 'success',
                message: "Votre addresse est correctement sauvegarder."
            );

            return $this->redirectToRoute('app_account_addresses');
        }
        
        return $this->render('account/addressForm.html.twig', [
            'addressForm' => $form
        ]);
    }
}
