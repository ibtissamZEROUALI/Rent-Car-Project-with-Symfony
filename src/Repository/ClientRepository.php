<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function add(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Client[] Returns an array of Client objects
     */
   /*public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }*/

    public function findOneByEmail($Email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Email = :Email')
            ->setParameter('Email', $Email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findOneByPhone($Phone): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Phone = :Phone')
            ->setParameter('Phone', $Phone)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findOneByCin($Cin): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Cin = :Cin')
            ->setParameter('Cin', $Cin)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
