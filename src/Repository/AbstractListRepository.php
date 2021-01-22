<?php

namespace App\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use function array_key_exists;

abstract class AbstractListRepository extends ServiceEntityRepository
{

    /**
     * Fetches list of object with given filters.
     * @param array $filters
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getList(array $filters = [], int $offset = 0, int $limit = 0): array
    {
        $builder = $this->getListQueryBuilder($filters, $offset, $limit);

        return $builder->getQuery()->getResult();
    }

    /**
     * Fetches total number of results for given filters.
     * @param array $filters
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function getListCount(array $filters = []): int
    {
        $builder = $this->getQueryBuilder($filters)->select('COUNT(o)');

        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * create where clausure depends on type of field
     * @param QueryBuilder $builder
     * @param string $key
     * @param $value
     * @param string $prefix
     */
    protected function addWhere(QueryBuilder $builder, string $key, $value, $prefix = 'o')
    {
        switch (true) {
            case $this->getClassMetadata()->hasAssociation($key):
                $builder->andWhere($prefix . '.' . $key . ' = ' . ':' . $key);
                $builder->setParameter($key, $value);
                break;
            case is_array($value):
                $builder->andWhere($builder->expr()->in($prefix . '.' . $key, $value));
                break;
            default:
                $chars4 = substr($key, 0, 4);
                $chars2 = substr($key, 0, 2);
                $chars1 = substr($key, 0, 1);
                if (in_array($chars4, ['NULL'])) {
                    $realKey = substr($key, 4);
                    $op = substr($key, 0, 4);
                } elseif (in_array($chars2, ['>=', '<=', '!='])) {
                    $realKey = substr($key, 2);
                    $op = substr($key, 0, 2);
                } elseif (in_array($chars1, ['>', '<', '=', '%'])) {
                    $realKey = substr($key, 1);
                    $op = substr($key, 0, 1);
                    if ($op === '%') {
                        $op = 'LIKE';
                        $value = $value . '%';
                    }
                } else {
                    $realKey = $key;
                    $op = 'LIKE';
                    $value = '%' . $value . '%';
                }
                if (!in_array($op, ['NOTNULL', 'NULL'])) {
                    $paramName = 'param_' . substr(md5($op . '_' . $realKey), 0, 8);
                    $builder->andWhere($prefix . '.' . $realKey . ' ' . $op . ' ' . ':' . $paramName);
                    $builder->setParameter($paramName, $value);
                } else {
                    $builder->andWhere($prefix . '.' . $realKey . ' IS ' . (!$value ? 'NOT ' : '') . $op);
                }
                break;
        }
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     * @throws Exception
     */
    protected function getQueryBuilder(array $filters = []): QueryBuilder
    {
        $builder = $this->createQueryBuilder('o');

        $this->parseFilters($builder, $filters);

        return $builder;
    }

    /**
     * @param array $filters
     * @param int $offset
     * @param int $limit
     * @return QueryBuilder
     * @throws Exception
     */
    protected function getListQueryBuilder(array $filters = [], int $offset = 0, int $limit = 0): QueryBuilder
    {
        $builder = $this->getQueryBuilder($filters);

        $builder->setFirstResult($offset);

        if ($limit !== 0) {
            $builder->setMaxResults($limit);
        }
        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param array $filters
     * @param string $prefixO
     * @throws Exception
     */
    protected function parseFilters(QueryBuilder $builder, array $filters = [], $prefixO = 'o')
    {
        foreach ($filters as $key => $value) {
            $key = explode('.', $key);
            switch (true) {
                case (is_array($value)):
                    if ($key[0] == 'updated' || $key[0] == 'created') {
                        $builder->andWhere('o.' . $key[0] . ' >= :from_' . $key[0]);
                        $builder->andWhere('o.' . $key[0] . ' <= :to_' . $key[0]);
                        $builder->setParameter('from_' . $key[0], (new DateTime($value['from'])));
                        $builder->setParameter('to_' . $key[0], (new DateTime($value['to']))->modify('+1 day'));
                    } elseif (array_key_exists('from', $value)) {
                        if ($value['from']) {
                            $builder->andWhere($prefixO . '.' . $key[0] . ' >= :from_' . $key[0]);
                            $builder->setParameter('from_' . $key[0], $value['from']);
                        }
                        if ($value['to']) {
                            $builder->andWhere($prefixO . '.' . $key[0] . ' <= :to_' . $key[0]);
                            $builder->setParameter('to_' . $key[0], $value['to']);
                        }
                    } else {
                        $builder->andWhere($prefixO . '.' . $key[0] . ' in (:paramIn' . $key[0] . ')')
                            ->setParameter('paramIn' . $key[0], $value);
                    }
                    break;
                case (count($key) == 2):
                    $this->addWhere($builder, $key[1], $value, $key[0]);
                    break;
                default:
                    $this->addWhere($builder, end($key), $value, $prefixO);
                    break;
            }
        }
    }

}
