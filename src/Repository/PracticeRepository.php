<?php

namespace App\Repository;

use App\Entity\Practice;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Practice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Practice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Practice[]    findAll()
 * @method Practice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PracticeRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Practice::class);
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

    public function getPracticeByName(Request $request)
    {
        $entityManager = $this->getEntityManager();

        $region = $request->attributes->get('_region');
        $language = $request->attributes->get('_locale');

        $query = $entityManager->createQuery(
            "SELECT p FROM App\Entity\Practice p INNER JOIN p.translations t
                WHERE p.regions LIKE :region AND p.languages LIKE :language AND t.locale LIKE :local
                AND p.highlighted = true ORDER BY t.title ASC "
        )->setParameters(array(
                'language' => '%'.$language.'%',
                'region' => '%'.$region.'%',
                'local' => '%'.$language.'%',
        ));

        return $query;
    }

    public function getPracticesIfLawyers()
    {
        $practices = $this->findAll();
        foreach ($practices as $key => $practice) {
            $lawyers = $practice->getLawyers();
            $lawyersSecondary = $practice->getLawyersSecondary();
            if (count($lawyers) == 0 && count($lawyersSecondary) == 0) {
                unset($practices[$key]);
            }
        }
        return $practices;
    }

    // /**
    //  * @return Practice[] Returns an array of Practice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Practice
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
