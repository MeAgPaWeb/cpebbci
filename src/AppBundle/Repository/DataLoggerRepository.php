<?php

namespace AppBundle\Repository;

/**
 * DataLoggerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DataLoggerRepository extends \Doctrine\ORM\EntityRepository
{
  public function getDataLoggers($library){
		return $this->createQueryBuilder('dataLogger')
	        ->select('dataLogger')
	        ->innerJoin('dataLogger.room', 'room')
	        ->innerJoin('room.library', 'library')
	        ->where('library = :library')
	        ->setParameter('library', $library)
	        ->getQuery()
	        ->getResult();
	}

  public function getAvgHT($room, $offset, $limit){
    return $this->createQueryBuilder('d')
          ->select('avg(d.temperature) as meanAvT, avg(d.rh) as meanAvH')
          ->innerJoin('d.room', 'room')
          ->where('room = :room')
          ->groupBy('d.room')
          ->setParameter('room', $room)
          ->setMaxResults($limit)
          ->setFirstResult($offset)
          ->getQuery()
          ->getArrayResult();
  }

  public function getDataLoggersValid($room, $offset, $limit){
    return $this->createQueryBuilder('d')
          ->select('d')
          ->where('d.room = :room')
          ->setParameter('room', $room->getId())
          ->setMaxResults($limit)
          ->setFirstResult($offset)
          ->getQuery()
          ->getResult();
  }

  public function getCantDataLogger($room){
    return $this->createQueryBuilder('d')
            ->select('COUNT(d)')
            ->where('d.room = :room')
            ->setParameter('room', $room->getId())
            ->getQuery()
            ->getSingleScalarResult();
      }

}
