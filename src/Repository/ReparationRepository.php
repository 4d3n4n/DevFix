<?php

namespace App\Repository;

use App\Entity\Reparation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reparation>
 *
 * @method Reparation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reparation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reparation[]    findAll()
 * @method Reparation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReparationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reparation::class);
    }

    public function createReparation($tarif, $appareil, $user)
    {
        $reparation = new Reparation();
        $reparation->setObservation($tarif->getIdPanne()->getLibPanne())
            ->setDateDemande(new \DateTime())
            ->setDateMajDemande(new \DateTime())
            ->setIdPanne($tarif->getIdPanne())
            ->setIdAppareil($appareil)
            ->setIdUtilisateur($user);

        $this->_em->persist($reparation);

        return $reparation;
    }

}
