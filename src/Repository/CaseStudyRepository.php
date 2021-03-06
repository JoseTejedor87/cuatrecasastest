<?php

namespace App\Repository;

use App\Entity\CaseStudy;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method CaseStudy|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaseStudy|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaseStudy[]    findAll()
 * @method CaseStudy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaseStudyRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, CaseStudy::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->createQueryBuilder('c')
                ->join('c.translations', 't')
                ->where('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return null;
    }

    public function findByActivities($activities, $exclude=null)
    {
        $activities = array_map(
            function ($activity) {
                return $activity->getId();
            },
            $activities
        );

        $query = $this->createPublishedQueryBuilder('c')
            ->join('c.activities', 'a')
            ->andWhere('a.id IN (:ids)')
            ->setParameter('ids', $activities);

        if ($exclude) {
            $query->andWhere('c.id != (:exclude)');
            $query->setParameter('exclude', $exclude);
        }

        $dic = $query->select('c.id')->groupBy('c.id')->setMaxResults(10)->getQuery()->getResult();
        $idArrays = [];
        foreach ($dic as $key => $value ){  array_push($idArrays,($value['id']));     }

        $query2 = $this->createPublishedQueryBuilder('c')
                        ->andWhere('c.id IN (:idsCases)')
                        ->setParameter('idsCases', $idArrays)
                        ->setMaxResults(10);
                    
        return  $query2->getQuery()->getResult();
    }

    public function findByLawyer($lawyer)
    {
        return $query = $this->createPublishedQueryBuilder('c')
            ->join('c.lawyers', 'l')
            ->andWhere('l.id = :lawyer_id')
            ->setParameter('lawyer_id', $lawyer->getId())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findCasesByRegion($forcePlace){

        $qb = $this->createPublishedQueryBuilder('c')->join('c.lawyers', 'l');
        $qp = clone $qb;

        if($forcePlace != null ){
            $this->priorBuilderClause($qp,'l.office',$forcePlace);
        }else {
            $this->priorBuilderClause($qp,'l.office');    
        }

        $casesPrior = $qp->getQuery()->getResult();
        $casesRest = $qb->getQuery()->getResult();

        $cases = [];
        foreach ($casesPrior as $key => $item) {
            $cases[$item->getId()] = $item;
        }
  
        foreach ($casesRest as $key => $item) {
            if (!isset($cases[$item->getId()])){
                array_push($cases, $item);
            }
        }  
        // limit a 10 casos
        return array_slice($cases,0,10);
    }

    public function findByLawyersId($lawyers)
    {
        return $query = $this->createPublishedQueryBuilder('c')
            ->join('c.lawyers', 'l')
            ->andWhere('l.id IN (:lawyers)')
            ->setParameter('lawyers', $lawyers)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return CaseStudy[] Returns an array of CaseStudy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CaseStudy
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
