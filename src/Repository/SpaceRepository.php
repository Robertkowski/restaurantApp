<?php

namespace App\Repository;

use App\Entity\Space;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Space|null find($id, $lockMode = null, $lockVersion = null)
 * @method Space|null findOneBy(array $criteria, array $orderBy = null)
 * @method Space[]    findAll()
 * @method Space[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpaceRepository extends AbstractListRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Space::class);
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
