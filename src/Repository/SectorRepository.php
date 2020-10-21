<?php

namespace App\Repository;

use App\Entity\Sector;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Sector|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sector|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sector[]    findAll()
 * @method Sector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectorRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Sector::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->createQueryBuilder('s')
                ->join('s.translations', 't')
                ->where('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return null;
    }

    public function getSectorsByName(Request $request)
    {
        $entityManager = $this->getEntityManager();

        $region = $request->attributes->get('_region');
        $language = $request->attributes->get('_locale');

        $query = $entityManager->createQuery(
            "SELECT s FROM App\Entity\Sector s INNER JOIN s.translations t
                WHERE s.regions LIKE :region AND s.languages LIKE :language AND t.locale LIKE :local
                AND s.highlighted = true ORDER BY t.title ASC "
        )->setParameters(array(
                'language' => '%'.$language.'%',
                'region' => '%'.$region.'%',
                'local' => '%'.$language.'%',
        ));

        return $query;
    }

    public function getSectorsIfLawyers()
    {
        $sectors = $this->findAll();
        foreach ($sectors as $key => $sector) {
            $lawyers = $sector->getLawyers();
            $lawyersSecondary = $sector->getLawyersSecondary();
            if (count($lawyers) == 0 && count($lawyersSecondary) == 0) {
                unset($sectors[$key]);
            }
        }
        return $sectors;
    }

    // /**
    //  * @return Sector[] Returns an array of Sector objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sector
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
