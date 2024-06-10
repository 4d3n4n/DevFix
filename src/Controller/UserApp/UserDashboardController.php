<?php

namespace App\Controller\UserApp;

use App\Entity\Utilisateur;
use App\Repository\DevisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use App\Entity\Ville;


class UserDashboardController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/user-dashboard', name: 'app_user-dashboard')]
    public function index(Request $request, DevisRepository $devisRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_homepage');
        }

        $devis = $devisRepository->findBy(['idUtilisateur' => $user]);

        return $this->render('user_app/user-dashboard.html.twig', [
            'controller_name' => 'UserDashboardController',
            'devis' => $devis,
        ]);
    }

    #[Route('/edit-user', name: 'app_edit_user', methods: ['POST'])]
    public function editUser(Request $request, ManagerRegistry $doctrine, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $csrfToken = new CsrfToken('edit-user', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_homepage');
        }

        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $email = $request->request->get('email');
        $numero = $request->request->get('numero');
        $adresse = $request->request->get('adresse');
        $complementAdresse = $request->request->get('complement_adresse');
        $nomVille = $request->request->get('ville');
        $cp = $request->request->get('cp');

        $ancienMdp = $request->request->get('mdp');
        $nouveauMdp = $request->request->get('newMdp');
        $confirmationMdp = $request->request->get('confirmNewMdp');


        $validationErrors = [];
        if (!preg_match('/^[a-zA-Z ]+$/', $nom)) {
            $validationErrors[] = 'Le nom contient des caractères invalides.';
        }

        if (!preg_match('/^[a-zA-Z ]+$/', $prenom)) {
            $validationErrors[] = 'Le prénom contient des caractères invalides.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = 'L\'adresse email est invalide.';
        }

        if (!preg_match('/^\d{10}$/', $numero)) {
            $validationErrors[] = 'Le numéro de téléphone doit contenir 10 chiffres.';
        }

        if (!preg_match('/^[a-zA-Z ]+$/', $nomVille)) {
            $validationErrors[] = 'La ville contient des caractères invalides.';
        }

        if (!preg_match('/^\d{5}$/', $cp)) {
            $validationErrors[] = 'Le code postal doit contenir 5 chiffres.';
        }

        if ($nom === null || $prenom === null || $email === null || $numero === null || $adresse === null || $nomVille === null || $cp === null) {
            $validationErrors[] = 'Tous les champs requis doivent être renseignés.';
        }

        if (count($validationErrors) > 0) {
            foreach ($validationErrors as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('app_user-dashboard');
        }



        if ($nouveauMdp && $confirmationMdp) {
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nouveauMdp)) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 8 caractères, incluant au moins un chiffre et un caractère spécial.');
                return $this->redirectToRoute('app_user-dashboard');
            }
            
            if ($nouveauMdp !== $confirmationMdp) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_user-dashboard');
            }

            if (!password_verify($ancienMdp, $user->getMdp())) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_user-dashboard');
            }

            if ($nouveauMdp === $ancienMdp) {
                $this->addFlash('error', 'Le nouveau mot de passe doit être différent de l\'ancien.');
                return $this->redirectToRoute('app_user-dashboard');
            }

            $user->setMdp(password_hash($nouveauMdp, PASSWORD_ARGON2ID));
        }

        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setNumero($numero);
        $user->setAdresse($adresse);
        $user->setComplementAdresse($complementAdresse);
        $user->setDateMajUtilisateur(new \DateTime());

        $entityManager = $doctrine->getManager();
        $villeEntity = $entityManager->getRepository(Ville::class)->findOneBy(['nomVille' => $nomVille]);
        
        if (!$villeEntity) {
            $villeEntity = new Ville();
            $villeEntity->setNomVille($nomVille);
            $villeEntity->setCodeInsee($cp);
            $villeEntity->setCodePostal($cp);
            $entityManager->persist($villeEntity);
        }

        $user->setIdVille($villeEntity);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

        return $this->redirectToRoute('app_user-dashboard');
    }

}
