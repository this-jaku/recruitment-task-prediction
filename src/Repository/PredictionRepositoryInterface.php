<?php

namespace App\Repository;

use App\Entity\Prediction;

interface PredictionRepositoryInterface
{
    public function getById(int $id): ?Prediction;

    public function getAll(): ?array;

    public function save(Prediction $predictionEntity): bool;
}
