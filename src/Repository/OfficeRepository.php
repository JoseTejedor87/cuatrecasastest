<?php

namespace App\Repository;

use App\Entity\Office;
use App\Entity\Lawyers;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Office|null find($id, $lockMode = null, $lockVersion = null)
 * @method Office|null findOneBy(array $criteria, array $orderBy = null)
 * @method Office[]    findAll()
 * @method Office[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation,Office::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->findOneBy(['slug' => $slug]);
        }
        return null;
    }

}
