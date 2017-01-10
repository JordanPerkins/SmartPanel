<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class ResetRepository extends EntityRepository
{
  public function findReset($id, $hash)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.id = :id and u.hash = :hash')
        ->setParameter('id', $id)
        ->setParameter('hash', $hash)
        ->getQuery();
    return $results->getOneOrNullResult();
  }
  public function checkReset($user)
  {
   $results = $this->createQueryBuilder('u')
      ->where('u.uid = :id')
      ->setParameter('id', $user)
      ->orderBy('u.id', 'DESC')
      ->setMaxResults(1)
      ->getQuery();
  return $results->getOneOrNullResult();
}
}
