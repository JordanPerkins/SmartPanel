<?php
/* Repository Class for the IP Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch tIP info.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class IPRepository extends EntityRepository
{
  /* This will fetch all IP's of certain type belonging to a server
   * It returns an array. */
  public function findBySID($sid, $type)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.version = :type and u.sid = :sid')
        ->setParameter('type', $type)
        ->setParameter('sid', $sid)
        ->getQuery();
    return $results->getResult();
  }

  /* This will find a particular template by file name.
  * If multiple results are found then it will return null */
  /*public function findByFile($file)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.file = :file')
        ->setParameter('file', $file)
        ->getQuery();
    return $results->getOneOrNullResult();
  }*/

  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }


}
