<?php

namespace App\Service\Prediction;

use App\Entity\Prediction;
use App\Repository\PredictionRepositoryInterface;
use App\Service\Core\Exception\RepositoryException;
use App\Service\Core\Exception\ResourceNotFoundException;

class PredictionManagerService
{
    /** @var PredictionRepositoryInterface */
    private $predictionRepository;

    public function __construct(PredictionRepositoryInterface $predictionRepository)
    {
        $this->predictionRepository = $predictionRepository;
    }

    /**
     * @param int $eventId
     * @param string $marketType
     * @param string $prediction
     * @throws Exception\PredictionEntityException
     * @throws RepositoryException
     */
    public function createPrediction(int $eventId, string $marketType, string $prediction)
    {
        $predictionEntity = new Prediction($eventId, $marketType, $prediction);
        $this->predictionRepository->save($predictionEntity);
    }

    /**
     * @return Prediction[]
     * @throws RepositoryException
     */
    public function list(): array
    {
        return $this->predictionRepository->getAll();
    }

    /**
     * @param int $id
     * @param string $status
     * @throws Exception\PredictionEntityException
     * @throws RepositoryException
     * @throws ResourceNotFoundException
     */
    public function changeStatus(int $id, string $status): void
    {
        $predictionEntity = $this->predictionRepository->getById($id);
        if (!$predictionEntity) {
            throw new ResourceNotFoundException('Prediction not found.');
        }
        $predictionEntity->changeStatus($status);
        $this->predictionRepository->save($predictionEntity);
    }
}
