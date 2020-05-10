<?php

namespace App\Repository\DoctrineORM;

use App\Entity\Prediction;
use App\Repository\PredictionRepositoryInterface;
use App\Service\Core\Exception\RepositoryException;
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

    /**
     * @param Prediction $predictionEntity
     * @throws RepositoryException
     */
    public function save(Prediction $predictionEntity): void
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($predictionEntity);
            $em->flush();
        } catch (\Exception $e) {
            throw new RepositoryException('Failed to save Prediction in database.', 0, $e);
        }
    }

    /**
     * @param int $id
     * @return Prediction|null
     * @throws RepositoryException
     */
    public function getById(int $id): ?Prediction
    {
        try {
            return $this->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException('Prediction not found.', 0, $e);
        }
    }

    /**
     * @return Prediction[]
     * @throws RepositoryException
     */
    public function getAll(): array
    {
        try {
            return $this->findAll();
        } catch (\Exception $e) {
            throw new RepositoryException('Failed to read Predictions.', 0, $e);
        }
    }
}
