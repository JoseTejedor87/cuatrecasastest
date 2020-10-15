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
            //->andWhere($alias.'.published = TRUE')
            ->andWhere($alias.'.regions LIKE :region')
            ->andWhere($alias.'.languages LIKE :language')
            ->setParameter('region', '%"'.$region.'"%')
            ->setParameter('language', '%"'.$language.'"%');
        return $queryBuilder;
    }
    
    public function orderByDaySentences($qb, $alias,$days =30){

        $date = new \DateTime();
        $date->modify('-'.$days.' days');
        $qb->andWhere($alias.'.publication_date > :date')->setParameter('date',  $date);
        
        return   $qb->orderBy($alias.'.publication_date', 'DESC');
    }

    public function filterByFieldsQueryBuilder($fields,$alias)
    {

        $queryBuilder = parent::createQueryBuilder($alias);
        foreach($fields as $key => $value){
            
            if( $key === 'languages' || $key === 'regions'){
                // se querra que cumpla con todas las condiciones de publicacion del mismo lenguaje y region
                if ($key === 'languages'){
                    foreach($value as $key => $lan){
                        $queryBuilder->andWhere($alias.'.languages LIKE :_lan'.$key)->setParameter('_lan'.$key, '%'.$lan.'%');
                    }
                }
                if ($key === 'regions'){
                    foreach($value as $key => $reg){
                        $queryBuilder->andWhere($alias.'.regions LIKE :_reg'.$key)->setParameter('_reg'.$key, '%'.$reg.'%');
                    }
                }
            }
            else if ($value != '' ){
                $queryBuilder->andWhere($alias.'.'.$key.' LIKE :'.$key)->setParameter($key, '%'.$value.'%');
            }
        }

        return $queryBuilder;
    }
}
