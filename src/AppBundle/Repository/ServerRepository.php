<?php
/* Repository Class for the Server Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch server entities.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ServerRepository extends EntityRepository
{
  /* This will fetch all servers belonging to a particular user
   * It takes the user ID as an argument and returns an array. */
  public function findAllByUID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.uid = :id')
        ->orderBy('u.id', 'DESC')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getResult();
  }

  /* This will find a particular server by server ID.
  * If multiple results are found then it will return null */
  public function findByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  // Return the server count based on user ID
  public function countByID($uid) {
    $results = $this->createQueryBuilder('u')
    ->select('count(u.id)')
    ->where('u.uid = :id')
    ->setParameter('id', $uid)
    ->getQuery();
    return $results->getSingleScalarResult();
  }

}
