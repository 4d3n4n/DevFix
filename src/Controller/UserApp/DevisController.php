<?php

namespace App\Controller\UserApp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tarif;
use App\Entity\Utilisateur;
use App\Entity\Appareil;
use App\Entity\Reparation;
use App\Entity\Devis;
use App\Repository\AppareilRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\DevisRepository;
use App\Repository\ReparationRepository;




class DevisController extends AbstractController
{
    #[Route('/devis', name: 'app_devis', methods: ['POST'])]
    public function showDevisForm(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $postData = $request->request->all();
        $tarifIds = $postData['tarifIds'] ?? [];

        $session->set('tarifIds', $tarifIds);
    
        $tarifs = [];
        foreach ($tarifIds as $tarifId) {
            if (preg_match('/^\d{1,2}$/', $tarifId)) {
                $tarif = $entityManager->getRepository(Tarif::class)->find($tarifId);
                if ($tarif) {
                    $tarifs[] = $tarif;
                }
            } else {
                throw new \InvalidArgumentException('Identifiant de tarif invalide.');
            }
        }
    
        return $this->render('user_app/devis.html.twig', [
            'tarifs' => $tarifs,
        ]);
    }

    #[Route('/devis', name: 'app_devis_redirect', methods: ['GET'])]
    public function showForm(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $tarifIds = $session->get('tarifIds', []);

        $tarifs = [];
        foreach ($tarifIds as $tarifId) {
            if (preg_match('/^\d{1,2}$/', $tarifId)) {
                $tarif = $entityManager->getRepository(Tarif::class)->find($tarifId);
                if ($tarif) {
                    $tarifs[] = $tarif;
                }
            } else {
                throw new \InvalidArgumentException('Identifiant de tarif invalide.');
            }
        }
    
        return $this->render('user_app/devis.html.twig', [
            'tarifs' => $tarifs,
        ]);
    }



    #[Route('/create-devis', name: 'app_create_devis', methods: ['POST'])]
    public function createDevis(
        Request $request,
        EntityManagerInterface $entityManager,
        AppareilRepository $appareilRepository,
        ReparationRepository $reparationRepository,
        DevisRepository $devisRepository
        ): Response {
        $entityManager->getConnection()->beginTransaction();
        try {
            $user = $this->getUser();
    
            $codeImei = $request->request->get('codeImei', 'AJOUTER IMEI PAR ADMIN');
            $numSerie = $request->request->get('numSerie', 'AJOUTER NUMERO SERIE PAR ADMIN');

            $validationErrors = [];

            if (!empty($codeImei) && !preg_match('/^\w{15}$/', $codeImei)) {
                $validationErrors[] = 'Le code IMEI doit contenir 15 caractères alphanumériques.';
            }
            
            if (!empty($numSerie) && !preg_match('/^\w{15}$/', $numSerie)) {
                $validationErrors[] = 'Le numéro de série doit contenir 15 caractères alphanumériques.';
            }
    
            $postData = $request->request->all();
            $tarifIds = $postData['tarifIds'] ?? [];

            if (empty($tarifIds)) {
                $validationErrors[] = 'Veuillez sélectionner au moins une panne.';
            }

            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $error) {
                    $this->addFlash('error', $error);
                }
                $entityManager->getConnection()->rollBack();
                return $this->redirectToRoute('app_devis');
            }
    
            $lastDevis = $entityManager->getRepository(Devis::class)->findOneBy([], ['idDevis' => 'DESC']);
            $numDevis = $lastDevis ? 'DEV' . str_pad((substr($lastDevis->getNumDevis(), 3) + 1), 3, '0', STR_PAD_LEFT) : 'DEV001';
    
            $tarif = $entityManager->getRepository(Tarif::class)->find($tarifIds[0]);
            $modele = $tarif->getIdModele();
    
            $appareil = $appareilRepository->findOneBy([
                'idUtilisateur' => $user,
                'idModele' => $modele,
                'codeImei' => $codeImei,
                'numSerie' => $numSerie,
            ]);
    
            if (!$appareil) {
                $appareil = $appareilRepository->createAppareil($user, $modele, $codeImei, $numSerie);
            }
    
            foreach ($tarifIds as $tarifId) {
                $tarif = $entityManager->getRepository(Tarif::class)->find($tarifId);
    
                if ($tarif && $appareil) {
                    $reparation = $reparationRepository->createReparation($tarif, $appareil, $user);
                    $devis = $devisRepository->createDevis($numDevis, $tarif, $reparation, $user);
                }
            }
    
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $this->redirectToRoute('app_user-dashboard');
    
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

}
