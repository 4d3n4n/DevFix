<?php

namespace App\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UtilisateurRepository;

class ClientController extends AbstractController
{
    #[Route('/admin/client', name: 'admin_client')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findByRole(3);

        return $this->render('admin_app/client/admin_client.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/admin/modification-client/{id}', name: 'modification_client')]
    public function modifierClient(Request $request, UtilisateurRepository $utilisateurRepository, int $id): Response
    {
        $utilisateur = $utilisateurRepository->find($id);
    
         
    if ($request->isMethod('POST')) {
       
        $prenom = $request->request->get('prenom');
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $numero = $request->request->get('numero');
        $adresse = $request->request->get('adresse');


        $utilisateurRepository->updateUser($utilisateur, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'numero' => $numero,
            'adresse' => $adresse,
            
        ]);
        
        return $this->redirectToRoute('admin_client');
    }

    return $this->render('admin_app/client/admin_modification_client.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}

    #[Route('/admin/ajouter-client', name: 'ajouter_client')]
    public function ajouterClient(): Response
    {
        return $this->render('admin_app/client/admin_ajouter_client.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/admin/supprimer-client', name: 'supprimer_client')]
    public function supprimerClient(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        
        $idUtilisateur = $request->query->get('id');
    
        $utilisateurRepository->deleteUser($idUtilisateur);
    
        return $this->redirectToRoute('admin_client');
    }



}
