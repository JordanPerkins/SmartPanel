<?php
/* Repository Class for the Node Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch node entities.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NodeRepository extends EntityRepository
{

  /* This will find a particular node by node ID.
  * If multiple results are found then it will return null */
  public function findByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

}
