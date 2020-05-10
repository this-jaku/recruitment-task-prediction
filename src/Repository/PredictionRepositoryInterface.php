<?php

namespace App\Repository;

use App\Entity\Prediction;
use App\Service\Core\Exception\RepositoryException;

interface PredictionRepositoryInterface
{
    /**
     * @param int $id
     * @return null|Prediction
     *
     * @throws RepositoryException
     */
    public function getById(int $id): ?Prediction;

    /**
     * @return array
     *
     * @throws RepositoryException
     */
    public function getAll(): array;

    /**
     * @param Prediction $predictionEntity
     *
     * @throws RepositoryException
     */
    public function save(Prediction $predictionEntity): void;
}
