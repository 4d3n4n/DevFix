<?php

namespace App\Repository;

use App\Entity\Appareil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AppareilRepository;


/**
 * @extends ServiceEntityRepository<Appareil>
 *
 * @method Appareil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appareil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appareil[]    findAll()
 * @method Appareil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppareilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appareil::class);
    }


    public function createAppareil($user, $modele, $codeImei, $numSerie)
    {
        $appareil = new Appareil();
        $appareil->setCodeImei($codeImei)
            ->setNumSerie($numSerie)
            ->setDateCreationAppareil(new \DateTime())
            ->setIdUtilisateur($user)
            ->setIdModele($modele);

            $typeAppareil = $this->findOneBy(['idModele' => $modele]);
            if ($typeAppareil) {
            $appareil->setIdTypeAppareil($typeAppareil->getIdTypeAppareil());
        }

        $this->_em->persist($appareil);

        return $appareil;
    }

}
