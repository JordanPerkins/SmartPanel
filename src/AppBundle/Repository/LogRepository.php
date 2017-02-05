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

}
