<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/connexion', name: 'app_auth')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $referer = $request->headers->get('referer');

        $user = $this->security->getUser();

        if (($referer && str_contains($referer, '/devis')) && $user && in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('app_devis');
        }

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('homepage_admin');
            } elseif (in_array('ROLE_USER', $user->getRoles())) {
                return $this->redirectToRoute('app_user-dashboard');
            } elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
                return $this->redirectToRoute('app_homepage_employe');
        }
    }
    return $this->redirectToRoute('app_homepage');
}


    #[Route('/deconnexion', name: 'app_auth_deco')]
    public function deconnexion(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
}