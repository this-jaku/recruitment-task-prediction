<?php

namespace App\Entity;

use App\Service\Prediction\Exception\PredictionEntityException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PredictionRepository::class)
 */
class Prediction implements \JsonSerializable
{
    public const STATUS_WIN = 'win';
    public const STATUS_LOST = 'lost';
    public const STATUS_UNRESOLVED = 'unresolved';
    public const MARKET_TYPE_1X2 = '1x2';
    public const MARKET_TYPE_CORRECT_SCORE = 'correct_score';
    public const PREDICTION_1 = '1';
    public const PREDICTION_2 = '2';
    public const PREDICTION_X = 'X';
    private const PREDICTION_1X2_SEPARATOR = ':';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eventId;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('1x2', 'correct_score')")
     */
    private $marketType;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $prediction;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('win', 'lost', 'unresolved')")
     */
    private $status;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Prediction constructor.
     * @param int $eventId
     * @param string $marketType
     * @param string $prediction
     * @throws PredictionEntityException
     */
    public function __construct(int $eventId, string $marketType, string $prediction)
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->changeStatus(Prediction::STATUS_UNRESOLVED);
        $this->changePredictionAndMarketType($prediction, $marketType);
        $this->changeEventId($eventId);
    }

    /**
     * @param string $status
     * @throws PredictionEntityException
     */
    public function changeStatus(string $status): void
    {
        if (!in_array($status, [Prediction::STATUS_WIN, Prediction::STATUS_LOST, Prediction::STATUS_UNRESOLVED])) {
            throw new PredictionEntityException('Invalid market type.');
        }

        $this->status = $status;
    }

    /**
     * @param string $prediction
     * @param string $marketType
     * @throws PredictionEntityException
     */
    public function changePredictionAndMarketType(string $prediction, string $marketType): void
    {
        $marketType = trim($marketType);
        $prediction = trim($prediction);
        switch ($marketType) {
            case self::MARKET_TYPE_1X2:
                if (!in_array($prediction, [self::PREDICTION_1, self::PREDICTION_2, self::PREDICTION_X])) {
                    throw new PredictionEntityException('Invalid prediction.');
                }
                break;
            case self::MARKET_TYPE_CORRECT_SCORE:
                $predictionParts = explode(self::PREDICTION_1X2_SEPARATOR, $prediction);
                $firstNumber = isset($predictionParts[0]) ? abs((int)$predictionParts[0]) : null;
                $secondNumber = isset($predictionParts[1]) ? abs((int)$predictionParts[1]) : null;
                $comparativePrediction = "$firstNumber:$secondNumber";

                if ($comparativePrediction !== $prediction) {
                    throw new PredictionEntityException('Invalid prediction.');
                }
                break;
            default:
                throw new PredictionEntityException('Invalid market type.');
        }

        $this->marketType = $marketType;
        $this->prediction = $prediction;
    }

    public function changeEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getMarketType(): string
    {
        return $this->marketType;
    }

    /**
     * @return string
     */
    public function getPrediction(): string
    {
        return $this->prediction;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'evenet_id' => $this->eventId,
            'market_type' => $this->marketType,
            'prediction' => $this->prediction,
            'status' => $this->status,
        ];
    }


}
