<?php

namespace App\Repository\DoctrineORM;

use App\Entity\Prediction;
use App\Repository\PredictionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prediction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prediction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prediction[]    findAll()
 * @method Prediction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredictionRepository extends ServiceEntityRepository implements PredictionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    public function save(Prediction $predictionEntity): bool
    {
        // TODO: Implement save() method.
    }

    public function getById(int $id): ?Prediction
    {
        // TODO: Implement save() method.
    }

    public function getAll(): ?array
    {
        // TODO: Implement getAll() method.
    }
}
