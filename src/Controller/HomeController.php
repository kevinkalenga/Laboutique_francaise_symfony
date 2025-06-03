<?php

namespace App\Controller;

use App\Classe\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        
       
       $mail = new Mail();
       $mail->send('kevinkalenga10@gmail.com', 'Yves', 'Bonjour, teste de ma class', 'contenu');
        
        return $this->render('home/index.html.twig');
    }
}
