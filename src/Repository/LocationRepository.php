<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function add(Location $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Location $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function location()
    {
        $query = $this->createQueryBuilder('l')->select('v,u,l')
            ->from('Vehicule','v')
            ->from('User','u')
            ->leftJoin('l.Id_Vehicule', 'v')
            ->join('l.Id_User','u')
            ->getQuery();
        return $query->getResult();
    }

 /**
  * @return Location[] Returns an array of Location objects
  */
 public function findByReservation($value, $note): array
 {
     return $this->createQueryBuilder('l')
        ->where('l.Valide = :val')
        ->andWhere('l.Note = :note')
        ->setParameters(['val'=> $value, 'note' => $note])
        ->orderBy('l.id', 'ASC')
        ->getQuery()
        ->getResult()
     ;
 }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function findByLocation($value): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.Id_User != :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Location[] Returns an array of Location objects
     */
    public function findByRefus($val): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.Valide = :val')
            ->setParameter('val', $val)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByReservationClient($id): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.Id_Client = :val')
            ->setParameter('val', $id)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
//    public function findOneBySomeField($value): ?Location
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
