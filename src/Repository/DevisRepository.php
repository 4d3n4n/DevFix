<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devis>
 *
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }


    public function createDevis($numDevis, $tarif, $reparation, $user)
    {
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

        $this->_em->persist($devis);

        return $devis;
    }

}
