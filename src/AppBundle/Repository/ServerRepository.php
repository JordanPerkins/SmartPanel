<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ServerRepository extends EntityRepository
{
  public function findAllByUID($id)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.uid = :id')
        ->orderBy('u.id', 'DESC')
        ->setParameter('id', $id)
        ->getQuery();
    return $results->getResult();
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
