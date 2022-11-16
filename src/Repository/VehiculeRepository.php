<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function add(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function VehiculeFonctionne($statut){
        return $this->createQueryBuilder('v')
        ->where('v.StatusVehicule = :statut')
        ->setParameters(['statut'=> $statut])
        ->getQuery()
        ->getResult();
    }

    public function VehiculeEnPanne($statut)
    {
        return $this->createQueryBuilder('v')
            ->where('v.StatusVehicule = :statut')
            ->setParameters(['statut' => $statut])
            ->getQuery()
            ->getResult();
    }

    public function SelectPrixDay($id)
    {
        return $this->createQueryBuilder('v')
            ->select('v.PrixDay')
            ->where('v.id = :id')
            ->setParameters(['id' => $id])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function Select($Matricul)
    {
        return $this->createQueryBuilder('v')
            ->select('v.PrixDay')
            ->where('v.Matricul = :Matricul')
            ->setParameters(['Matricul' => $Matricul])
            ->getQuery()
            ->getResult();
    }


    public function VP()
    {
        $qb = $this->createQueryBuilder('v')->select('p,v')
        ->from('Panne ', 'p')
            ->join('p.Id_Vehicule', 'v');

        return $qb->getQuery()->getResult();
    }


    /**
     * @return Vehicule[] Returns an array of Vehicule objects
     */
    public function findByType($Type): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.Id_TypeVehicule = :val')
            ->setParameter('val', $Type)
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByMarque($Marque): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.Id_Marque = :val')
            ->setParameter('val', $Marque)
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?Vehicule
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
  }
}
