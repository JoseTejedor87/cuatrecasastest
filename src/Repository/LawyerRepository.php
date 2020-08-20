<?php

namespace App\Repository;

use App\Entity\Lawyer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;


/**
 * @method Lawyer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lawyer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lawyer[]    findAll()
 * @method Lawyer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LawyerRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Lawyer::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->findOneBy(['slug' => $slug]);
        }
        return null;
    }

    public function findFilteredBy($arrayFields){

        $query = $this->filterByFieldsQueryBuilder($arrayFields,'l');
        $fechaInicial = '2015-01-01';

        if ( isset ( $arrayFields['fechaDesde'])){
            $query->andWhere('l.createdAt > :desde')
                ->setParameter('desde', $arrayFields['fechaDesde']->format('Y-m-d'));
        }

        if( isset ( $arrayFields['fechaHasta']) ){
            $query->andWhere('l.createdAt < :hasta')                
            ->setParameter('hasta', $arrayFields['fechaHasta']->format('Y-m-d'));
        }
        
        return  $query->getQuery()->getResult();
        

    }
}
