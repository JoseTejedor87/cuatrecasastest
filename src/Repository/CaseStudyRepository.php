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

    public function getRelatedCasesByActivities($casestudy)
    {
        $activities = array_map(
            function ($activity) {
                return $activity->getId();
            },
            $casestudy->getActivities()->getvalues()
        );
        return $this->createPublishedQueryBuilder('c')
            ->join('c.translations', 't')
            ->join('c.activities', 'a')
            ->andWhere('a.id IN (:ids)')
            ->andWhere('c.id != (:casestudy)')
            ->setParameter('ids', $activities)
            ->setParameter('casestudy', $casestudy->getId())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByActivity($activity)
    {
        return $this->createQueryBuilder('c')
            ->join('c.translations', 't')
            ->join('c.activities', 'a')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', [$activity->getId()])
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
