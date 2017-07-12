<?php
/* Repository Class for the Log Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch log entities.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LogRepository extends EntityRepository
{

  /* This will find all the logs belonging to a particular ID.
  * If multiple results are found then it will return null */
  public function findAllByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.uid = :id')
        ->orderBy('u.datetime', 'DESC')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getResult();
  }

  public function checkRateLimit($id, $limit, $period)
  {
    $results = $this->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.uid = :id AND TIME_DIFF(:now,u.datetime) <= :diff')
        ->setParameter('id', $id)
        ->setParameter('diff', $period)
        ->setParameter('now', new \DateTime("now"))
        ->getQuery()
        ->getSingleScalarResult();
    if ($results <= $limit) {
      return true;
    }
    return false;
  }

  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }

}
