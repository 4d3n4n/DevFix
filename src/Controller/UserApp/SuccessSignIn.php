<?php

namespace App\Controller\UserApp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuccessSignIn extends AbstractController
{
    #[Route('/success-sign-in', name: 'app_success_sign_in')]
    public function index(): Response
    {
        return $this->render('user_app/success-sign-in.html.twig', [
            'controller_name' => 'SuccessSignIn',
        ]);
    }
}
