<?php
/* Repository Class for the IP Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch tIP info.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class IPBlockRepository extends EntityRepository
{
  public function findByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getOneOrNullResult();
  }
}
