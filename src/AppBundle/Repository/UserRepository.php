<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
  public function findByUsername($username)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.username = :user')
        ->setParameter('user', $username)
        ->getQuery();
    return $results->getOneOrNullResult();
  }
  public function findByID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getOneOrNullResult();
  }
}
