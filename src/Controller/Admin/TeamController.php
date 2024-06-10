<?php

namespace App\Controller\Admin;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends AbstractController
{
    #[Route('/admin/team', name: 'admin_team')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        
        
        $utilisateurs = $utilisateurRepository->findByRole(2);

        return $this->render('admin_app/team/admin_team.html.twig', [
            'controller_name' => 'TeamController',
            'utilisateurs' => $utilisateurs,
        ]);
    }



  #[Route('/admin/modification-team/{id}', name: 'modification_team')]
public function modifierTeam(Request $request, UtilisateurRepository $utilisateurRepository, int $id): Response
{
    $utilisateur = $utilisateurRepository->find($id);

    if ($request->isMethod('POST')) {
       
        $prenom = $request->request->get('prenom');
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $numero = $request->request->get('numero');
        $adresse = $request->request->get('adresse');
        $complementAdresse = $request->request->get('complementAdresse');


        $utilisateurRepository->updateUser($utilisateur, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'numero' => $numero,
            'adresse' => $adresse,
            'complementAdresse' =>  $complementAdresse,
            
        ]);
        
        return $this->redirectToRoute('admin_team');
    }

    return $this->render('admin_app/team/admin_modification_team.html.twig', [
        'controller_name' => 'TeamController',
        'utilisateur' => $utilisateur,
    ]);
}



    #[Route('/admin/ajouter-team', name: 'ajouter_team')]
        public function ajouterTeam(UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            

            $utilisateurRepository->insertTeam($request);

            return $this->redirectToRoute('admin_team');
        }
        return $this->render('admin_app\team\admin_ajouter_team.html.twig');
    }




    #[Route('/admin/supprimer-team', name: 'supprimer_team')]
    public function supprimerTeam(UtilisateurRepository $utilisateurRepository, Request $request): Response
{
   
    
    $idUtilisateur = $request->query->get('id');
    
    $utilisateurRepository->deleteUser($idUtilisateur);

    return $this->redirectToRoute('admin_team');
}



 }









