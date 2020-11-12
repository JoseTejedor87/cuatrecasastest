<?php

namespace App\Repository;

use App\Entity\LegalNovelty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method LegalNovelty|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalNovelty|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalNovelty[]    findAll()
 * @method LegalNovelty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalNoveltyRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, LegalNovelty::class);
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
    public function findByLegalNovelty( $maxResult= 10)
    {
       

        $returnPublications = [];
        $results =  $this->createPublishedQueryBuilder('p');
        $place = $this->getNavigation()->getParams()->get('app.office_place')[$this->getNavigation()->getRegion()];
        $results->join('p.offices', 'o')
            ->andWhere('o.place = :place')
            ->setParameter('place', $place);
        foreach ($results as $key => $item) {
            $returnPublications[$item->getId()] = $item;
        }

        ///// consulta por todas las regiones
        $results =  $this->createPublishedQueryBuilder('p');
        $results =  $results->orderBy('p.publication_date', 'DESC')
                ->setMaxResults($maxResult)
                ->getQuery()
                ->getResult();

        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($results as $key => $item) {
            if (!isset($returnPublications[$item->getId()])) {
                array_push($returnPublications, $item);
            }
        }
        foreach ($returnPublications as $key => $value) {
            $value->photo = $this->getPhotoPathByFilter($value, 'publication_box');
            if (!$value->photo) {
                $value->photo = 'web/assets/img/cabecera_1920x1080_baja.jpg';
                // $value->photo = 'https://via.placeholder.com/800x400';
            }
        }
        return $returnPublications;
    }
    // /**
    //  * @return Desk[] Returns an array of Desk objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Desk
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
