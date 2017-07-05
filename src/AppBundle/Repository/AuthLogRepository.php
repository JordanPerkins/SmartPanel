<?php
/* The repository for the AuthLog class.
 * Created by Jordan Perkins
*/
namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class AuthLogRepository extends EntityRepository
{
  // Fetch all
  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }

}
