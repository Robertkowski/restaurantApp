<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends AbstractListRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getList(array $filters = [], int $offset = 0, int $limit = 0, $sortField = null, $sortType = null): array
    {
        $builder = $this->getListQueryBuilder($filters, $offset, $limit);

        if (!is_null($sortField) && !is_null($sortType)) {
            $builder->addOrderBy($sortField, $sortType);
        }

        return $builder->getQuery()->getResult();
    }
}
