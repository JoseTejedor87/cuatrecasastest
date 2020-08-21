<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Entity\Publishable;

class PublishableEntityRepository extends ServiceEntityRepository
{
    private $navigation;

    public function __construct(ManagerRegistry $registry, NavigationService $navigation, $entityClass)
    {
        parent::__construct($registry, $entityClass);
        $this->navigation = $navigation;
    }

    protected function getNavigation() {
        return $this->navigation;
    }
    /**
     * Creates a new QueryBuilder instance that is prepopulated
     * for this entity name and add standard filters by Publishable
     * instances (language and region)
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    public function createPublishedQueryBuilder($alias, $indexBy = null)
    {
        $language = $this->navigation->getLanguage();
        $region = $this->navigation->getRegion();

        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);
        $queryBuilder
            ->andWhere($alias.'.regions LIKE :region')
            ->andWhere($alias.'.languages LIKE :language')
            ->setParameter('region', '%"'.$region.'"%')
            ->setParameter('language', '%"'.$language.'"%');
        return $queryBuilder;
    }

    public function filterByFieldsQueryBuilder($fields,$alias)
    {

        $queryBuilder = parent::createQueryBuilder($alias);
        foreach($fields as $key => $value){
            if ($value != ''){
                $queryBuilder->andWhere($alias.'.'.$key.' LIKE :'.$key)->setParameter($key, '%'.$value.'%');
            }
        }
        return $queryBuilder;
    }
}
