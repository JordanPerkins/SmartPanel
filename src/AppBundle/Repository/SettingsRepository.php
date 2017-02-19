<?php
/* Repository Class for the Settings Entity
 * Created by Jordan Perkins
 * Necessary to query database to fetch particular settings.
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository
{

  /* This will find a particular setting by it's setting value
  * If multiple results are found then it will return null */
  public function findBySetting($setting)
  {
    $results = $this->createQueryBuilder('u')
        ->where('u.setting = :setting')
        ->setParameter('setting', $setting)
        ->getQuery();
    return $results->getOneOrNullResult();
  }

  /* Fetch all settings */
  public function findAll()
  {
    $results = $this->createQueryBuilder('u')
        ->getQuery();
    return $results->getResult();
  }

}
