<?php
/* The repository for the User class.
 * Created by Jordan Perkins
 * Used to fetch information about a particular user.
*/
namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
  /* Used to fetch a user by username.
   * I try to avoid using this where an ID can be used
   * However in cases such as the "Password Reset" function it is necessary.
  */
  public function findByUsername($username)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.username = :user')
        ->setParameter('user', $username)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  // Fetch user using the user ID.
  public function findByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  // Fetch all
  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }

}
