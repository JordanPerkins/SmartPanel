<?php
/* Reset Repository
 * Created by Jordan Perkins
 * Used to fetch information required for password reset function.
 * This uses the Reset entity.
*/
namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class ResetRepository extends EntityRepository
{
  /* Finds a particular reset by ID and hash.
   * Used to check if a reset link is valid.
  */
  public function findReset($id, $hash)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id and u.hash = :hash')
        ->setParameter('id', $id)
        ->setParameter('hash', $hash)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  /* Queries the database based on user id .
   * It will return the latest record only.
   * This is to carry out time checks to prevent
   * abuse of the function. */
  public function checkReset($id)
  {
   $results = $this->createQueryBuilder('u')
      ->where('u.uid = :id')
      ->setParameter('id', $id)
      ->orderBy('u.id', 'DESC')
      ->setMaxResults(1)
      ->getQuery();
  return $results->getOneOrNullResult();
  }

}
