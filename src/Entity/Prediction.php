<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PredictionRepository::class)
 */
class Prediction
{
    /**
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
}
