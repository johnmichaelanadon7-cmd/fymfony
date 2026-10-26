<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Products at or below their own low-stock threshold, lowest stock first.
     *
     * @return list<Product>
     */
    public function findLowStock(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stock <= p.lowStockThreshold')
            ->orderBy('p.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
