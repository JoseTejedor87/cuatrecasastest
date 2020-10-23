<?php

namespace App\Repository;

use App\Entity\Lawyer;
use App\Repository\SectorRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;


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
    public function __construct(ManagerRegistry $registry, NavigationService $navigation, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, $navigation, Lawyer::class);
        $this->entityManager = $entityManager;
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->findOneBy(['slug' => $slug]);
        }
        return null;
    }

    public function findFilteredBy($arrayFields)
    {
        if (isset($arrayFields['fechaDesde']) && isset($arrayFields['fechaDesde'])) {
            $fechaDesde = $arrayFields['fechaDesde'];
            unset($arrayFields['fechaDesde']);
        }

        if (isset($arrayFields['fechaHasta'])  && isset($arrayFields['fechaHasta'])) {
            $fechaHasta = $arrayFields['fechaHasta'];
            unset($arrayFields['fechaHasta']);
        }

        $query = $this->filterByFieldsQueryBuilder($arrayFields, 'l');

        if (isset($fechaDesde)) {
            $query->andWhere('l.createdAt > :desde')
                ->setParameter('desde', $fechaDesde->format('Y-m-d'));
        }

        if (isset($fechaHasta)) {
            $query->andWhere('l.createdAt < :hasta')
            ->setParameter('hasta', $fechaHasta->format('Y-m-d'));
        }

        return  $query->getQuery()->getResult();
    }
}
