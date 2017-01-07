<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ServerRepository extends EntityRepository
{
  public function findAllByID($id)
  {
    return $this->createQueryBuilder('u')
        ->where('u.uid = :id')
        ->setParameter('id', $id)
        ->getQuery();
  }
}
