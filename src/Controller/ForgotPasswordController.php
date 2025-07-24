<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\ForgotPasswordTypeForm;
use App\Form\ResetPasswordTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Classe\Mail;




final class ForgotPasswordController extends AbstractController
{
    
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    #[Route('/mot-de-passe-oublier', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        //1. Formulaire 
        $form = $this->createForm(ForgotPasswordTypeForm::class);

        $form->handleRequest($request);

        //2. traitement du formulaire
        if($form->isSubmitted() && $form->isValid()) {
          //3. Si l'email renseigné par le user est en bdd
          $email = $form->get('email')->getData();
          //recup le user depuis la bdd 
          $user = $userRepository->findOneByEmail($email);

        //4.   Envoyer une notification à l'utilisateur
          $this->addFlash('success', 'Si votre adresse email existe, vous recevrez un mail pour réinitialiser votre mot de passe');
          
        //5. Si user existe, on reset le pwd et on envoie par mail le nouveau mdp   
          if($user) {
            //  5. a - Créer un token qu'on va stocker en bdd  
            $token = bin2hex(random_bytes(15));
            $user->setToken($token);

            $date = new DateTime();
            $date->modify('+10minutes');

            $user->setTokenExpiredAt($date);
           
            
            $this->em->flush();
            
            
            $mail = new Mail();
               $vars = [
                 'link' => $this->generateUrl('app_password_update', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
               ];
              $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Modification de votre mot de passe', 'forgotpassword.html', $vars);
          }

        }
        
        
        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),

        ]);
    }


    #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]
    public function update(Request $request, UserRepository $userRepository ,$token): Response
    {
         if(!$token) {
           return $this->redirectToRoute('app_password');
         }

        //  Si le token existe
          $user = $userRepository->findOneByToken($token);
          
         $now = new DateTime();
         
         if(!$user || $now > $user->getTokenExpiredAt()) {
             return $this->redirectToRoute('app_password');
         }
         
       
         
         $form = $this->createForm(ResetPasswordTypeForm::class, $user);

         $form->handleRequest($request); 

         if($form->isSubmitted() && $form->isValid()) {
            //  Traitement à effectuer 
            $user->setToken(null);
            $user->setTokenExpiredAt(null);
            $this->em->flush();
            $this->addFlash(
                type: 'success',
                message: "Votre mot de passe est correctement réinitialisé."
            );
             return $this->redirectToRoute('app_login');
         }
        
        
           return $this->render('password/reset.html.twig', [
              'form' => $form->createView()
           ]);
    }
}
