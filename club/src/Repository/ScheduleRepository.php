<?php

namespace App\Repository;

use App\Entity\Schedule;
use App\Helper\EnumStatus\ScheduleStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 *
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /** @return Schedule[] */
    public function findScheduleByDay(\DateTimeInterface $day, int $computerId): array
    {
        $qb = $this->createQueryBuilder('s');
        return $qb
            ->where('s.status = :active')
            ->orWhere('s.status = :wait')
            ->andWhere('s.dateStart <= :end')
            ->andWhere('s.computer = :computerId')
            ->setParameter('active', ScheduleStatus::ACTIVE->value)
            ->setParameter('wait', ScheduleStatus::WAIT_PAYMENT->value)
            ->setParameter('end', $day)
            ->setParameter('computerId', $computerId)
            ->getQuery()
            ->getResult();
    }

    public function findScheduleWeek(\DateTime $startDate): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateStart >= :date')
            ->setParameter('date', $startDate)
            ->andWhere('s.dateStart <= :endDate')
            ->andWhere('s.status != :cancelled')
            ->setParameter('cancelled', ScheduleStatus::CANCELLED->value)
            ->setParameter('endDate',
                (clone $startDate)
                    ->modify('+7 day')
                    ->setTime(23, 59)
            )
            ->getQuery()
            ->getResult();
    }

    public function add(Schedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Schedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Schedule[] Returns an array of Schedule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Schedule
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
