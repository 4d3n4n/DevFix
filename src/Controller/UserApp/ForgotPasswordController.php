<?php

namespace App\Controller\UserApp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

class ForgotPasswordController extends AbstractController
{
    private $csrfTokenManager;
    private $passwordHasher;
    private $entityManager;
    
    

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator): Response
    {
        
        if ($request->isMethod('POST')) {
           
            $email = $request->request->get('email');
            
            $resetToken = $this->csrfTokenManager->getToken('reset_password')->getValue();
           
            $request->getSession()->set('reset_token', $resetToken);
            $request->getSession()->set('reset_email', $email);
    
            
            $resetUrl = $urlGenerator->generate('reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);
    
            
            $email = (new Email())
                ->from('anthony9419@outlook.fr') //email entreprise
                ->to($email)
                ->subject('Réinitialisation du mot de passe')
                ->html('Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href="' . $resetUrl . '">Réinitialiser le mot de passe</a>');
    
            $mailer->send($email);
    
            
            return $this->redirectToRoute('app_homepage');
        }
    
        
        return $this->render('user_app/forgot-password.html.twig');
    }


    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(Request $request): Response
    {
        $resetToken = $request->getSession()->get('reset_token');

        if ($resetToken) {
            
            return $this->render('user_app/reset-password.html.twig', [
                'resetToken' => $resetToken,
            ]);
        } else {
            
            return $this->redirectToRoute('app_forgot_password');
        }
    }


    
    #[Route('/reset-password/submit', name: 'reset_password_submit')]
    public function resetPasswordSubmit(Request $request): Response
    {

        $email = $request->getSession()->get('reset_email');

        $newPassword = $request->request->get('new_password');
        
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneByEmail($email);

        if ($user) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setMdp($hashedPassword);

            $this->utilisateurRepository->updatePassword($user);
            
            return $this->redirectToRoute('app_homepage');
        } else {
            
            return $this->redirectToRoute('app_forgot_password');
        }
    }
}
