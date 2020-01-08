<?php

namespace AppBundle\Repository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    public function getProductsToSync($limit){

        $qb = $this->createQueryBuilder('p');
        $q  = $qb->select(array('p'))
            ->where(
                $qb->expr()->gt('p.stock', 0)
            )->andWhere(
                $qb->expr()->isNull('p.shopifyProductId')
            )
            ->setMaxResults($limit)
            ->getQuery();
//        var_dump($q->getSQL());exit;

        return $q->getResult();
    }
}
