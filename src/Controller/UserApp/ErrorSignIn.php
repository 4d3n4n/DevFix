<?php

namespace App\Controller\UserApp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorSignIn extends AbstractController
{
    #[Route('/error-sign-in', name: 'app_error_sign_in')]
    public function index(): Response
    {
        return $this->render('user_app/error-sign-in.html.twig', [
            'controller_name' => 'ErrorSignIn',
        ]);
    }
}
