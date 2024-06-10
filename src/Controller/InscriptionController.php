<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\AppareilRepository;
use App\Entity\Devis;
use App\Entity\Tarif;
use App\Entity\Appareil;
use App\Entity\Reparation;


use App\Entity\Utilisateur;

class InscriptionController extends AbstractController
{
    private $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('/inscription', name: 'app_inscription', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, AppareilRepository $appareilRepository): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $mdp = $request->request->get('mdp');
            $numero = $request->request->get('numero');
            $adresse = $request->request->get('adresse');
            $complementAdresse = $request->request->get('complement_adresse');
            $nomVille = $request->request->get('ville');
            $cp = $request->request->get('cp');

            $codeImei = $request->request->get('codeImei', 'AJOUTER IMEI PAR ADMIN');
            $numSerie = $request->request->get('numSerie', 'AJOUTER NUMERO SERIE PAR ADMIN');
            $postData = $request->request->all();
            $tarifIds = $postData['tarifIds'] ?? [];

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

            if ($this->utilisateurRepository->findOneBy(['email' => $email])) {
                $validationErrors[] = 'Cet email est déjà utilisé.';
            }

            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $mdp)) {
                $validationErrors[] = 'Le mot de passe doit contenir au moins 8 caractères, incluant au moins un chiffre et un caractère spécial.';
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

            if ($nom === null || $prenom === null || $email === null || $mdp === null || $numero === null || $adresse === null || $nomVille === null || $cp === null) {
                $validationErrors[] = 'Tous les champs requis doivent être renseignés.';
            }

            if (empty($tarifIds)) {
                $validationErrors[] = 'Veuillez sélectionner au moins une panne.';
            }

            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('app_devis');
            }

            $user = $this->utilisateurRepository->insertUser($nom, $prenom, $email, $mdp, $numero, $adresse, $complementAdresse, $nomVille, $cp);

            if ($user) {
                $lastDevis = $entityManager->getRepository(Devis::class)->findOneBy([], ['idDevis' => 'DESC']);
                $numDevis = $lastDevis ? 'DEV' . str_pad((substr($lastDevis->getNumDevis(), 3) + 1), 3, '0', STR_PAD_LEFT) : 'DEV001';
        
                foreach ($tarifIds as $tarifId) {
                    $tarif = $entityManager->getRepository(Tarif::class)->find($tarifId);
        
                    if ($tarif) {
                        $modele = $tarif->getIdModele();
                        $marque = $modele->getIdMarque();
                        $typeAppareils = $marque->getidTypeAppareils();
        
                        if (!$typeAppareils->isEmpty()) {
                            $typeAppareil = $typeAppareils->first();
        
                            $appareil = $appareilRepository->findOneBy([
                                'idUtilisateur' => $user,
                                'idModele' => $modele,
                                'idTypeAppareil' => $typeAppareil,
                                'codeImei' => $codeImei,
                                'numSerie' => $numSerie,
                            ]);
                        }
        
                        if (!$appareil) {
                            $appareil = new Appareil();
                            $appareil->setCodeImei($codeImei)
                                    ->setNumSerie($numSerie)
                                    ->setDateCreationAppareil(new \DateTime())
                                    ->setIdUtilisateur($user)
                                    ->setIdModele($modele)
                                    ->setIdTypeAppareil($typeAppareil);
                            $entityManager->persist($appareil);
                        }

                        if ($appareil) {
                            $typePanne = $tarif->getIdPanne();
                            $reparation = new Reparation();
                            $reparation->setObservation($typePanne->getLibPanne())
                                    ->setDateDemande(new \DateTime())
                                    ->setDateMajDemande(new \DateTime())
                                    ->setIdPanne($typePanne)
                                    ->setIdAppareil($appareil)
                                    ->setIdUtilisateur($user);
                            $entityManager->persist($reparation);
                        }

                        if ($reparation) {
                            $devis = new Devis();
                            $devis->setNumDevis($numDevis)
                                ->setDateDevis(new \DateTime())
                                ->setPrixTtc($tarif->getMontant())
                                ->setCommentaireDevis($tarif->getIdPanne()->getLibPanne())
                                ->setDateRestitution(new \DateTime('+1 week'))
                                ->setStatut(0)
                                ->setDateMajDevis(new \DateTime())
                                ->setIdReparation($reparation)
                                ->setIdUtilisateur($user);
                            $entityManager->persist($devis);
                        }

                        $entityManager->flush();
                    }
                }

                return $this->redirectToRoute('app_success_sign_in');
            }
        }

        return $this->render('user_app/devis.html.twig');
    }
    
}
