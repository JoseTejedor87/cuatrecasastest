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
        parent::__construct($registry, $navigation, Office::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->findOneBy(['slug' => $slug]);
        }
        return null;
    }
    public function getOfficesIfLawyers()
    {
        $offices = $this->findAll();
        foreach ($offices as $key => $office) {
            $lawyers = $office->getLawyer();
            if (count($lawyers) == 0) {
                unset($offices[$key]);
            }
        }
        return $offices;
    }

    public function getOfficesByName(Request $request)
    {
        $entityManager = $this->getEntityManager();

        $region = $request->attributes->get('_region');
        $language = $request->attributes->get('_locale');

        $query = $entityManager->createQuery(
            "SELECT o FROM App\Entity\Office o INNER JOIN o.translations t
                WHERE o.regions LIKE :region AND o.languages LIKE :language AND t.locale LIKE :local
                ORDER BY t.city ASC "
        )->setParameters(array(
                'language' => '%'.$language.'%',
                'region' => '%'.$region.'%',
                'local' => '%'.$language.'%',
        ));

        return $query;
    }
}
