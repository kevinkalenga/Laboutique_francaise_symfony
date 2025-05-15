<?php

namespace App\Controller;

use App\Form\RegisterUserTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(): Response
    {
        $form = $this->createForm(RegisterUserTypeForm::class);
        return $this->render('register/index.html.twig', [
            'registerForm'=> $form->createView()
        ]);
    }
}
