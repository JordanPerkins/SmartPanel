<?php
/* Repository Class for the AdminLog Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch AdminLog entities.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdminLogRepository extends EntityRepository
{

  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }

}
