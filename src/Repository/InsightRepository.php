<?php

namespace App\Repository;

use App\Entity\Insight;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Insight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Insight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Insight[]    findAll()
 * @method Insight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsightRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Insight::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->createQueryBuilder('p')
                ->join('p.translations', 't')
                ->where('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return null;
    }

    public function getInsightByLawyers($lawyers)
    {
        return $this->createPublishedQueryBuilder('i')
            //->join('i.translations', 't')
            ->join('i.lawyers', 'l')
            ->andWhere('l.id IN (:ids)')
            ->setParameter('ids', $lawyers)
                ->getQuery()
                ->getResult();
    }

    public function getInsightsPriorFor($arrayCriteria){
        $query = $this->createQueryBuilder('i')->join('i.lawyers', 'l');

        $this->priorBuilderClause($query,'l.office');
        $this->orderByDaySentences($query,'i','10', 'id');
        
        foreach ($arrayCriteria as $key => $value) {
            $query->andWhere('i.'.$key.' = '.$value);
        }
        $query->setMaxResults(1);
        //dd($query->getQuery()->getDQL());
        return $query->getQuery()->getResult();      

    }

    public function getPublishedRelatedInsights($insight)
    {
        $relatedInsights = array_map(
            function ($insight) {
                return $insight->getId();
            },
            $insight->getRelatedInsights()->getvalues()
        );

        return $this->createPublishedQueryBuilder('i')
            ->join('i.translations', 't')
            ->andWhere('i.id IN (:ids)')
            ->setParameter('ids', $relatedInsights)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Insight[] Returns an array of Insight objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Insight
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
