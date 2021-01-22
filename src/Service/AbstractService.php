<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class AbstractService
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param $entity
     * @param bool $flush
     */
    public function saveEntity($entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

}