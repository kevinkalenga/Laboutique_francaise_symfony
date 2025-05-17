<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PasswordUserForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // on crée un formumaire et on lui passe une option passwordHasher qui verif le mdp
        $form = $this->createForm(PasswordUserForm::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        // Ecoute la requette

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                type: 'success',
                message: "Votre mot de passe est correctement mis à jour ."
            );
            
        }
       
        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }
}
