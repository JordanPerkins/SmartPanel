<?php
/* Repository Class for the Template Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch template info.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TemplateRepository extends EntityRepository
{
  /* This will fetch all templates of a particular type.
   * It takes the type as an argument and returns an array. */
  public function findByType($type)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.type = :type')
        ->setParameter('type', $type)
        ->getQuery();
    return $results->getResult();
  }

  /* This will find a particular template by file name.
  * If multiple results are found then it will return null */
  public function findByFile($file)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.file = :file')
        ->setParameter('file', $file)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }


}
