<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageEmployeController extends AbstractController
{
    #[Route('/employe/homepage', name: 'app_homepage_employe')]
    public function index(): Response
    {
        return $this->render('employe_app/homepage.html.twig', [
            'controller_name' => 'HomepageEmployeController',
        ]);
    }
}
