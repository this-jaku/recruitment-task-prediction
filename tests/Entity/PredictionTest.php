<?php

namespace App\Tests\Entity;

use App\Entity\Prediction;
use App\Service\Prediction\Exception\PredictionEntityException;
use PHPUnit\Framework\TestCase;

class PredictionTest extends TestCase
{
    public function testSuccessfulCreatePredictionWithDefaultStatusAndDates()
    {
        $eventId = 66444;
        $prediction = new Prediction($eventId, Prediction::MARKET_TYPE_1X2, Prediction::PREDICTION_X);

        $this->assertEquals($eventId, $prediction->getEventId());
        $this->assertEquals(Prediction::MARKET_TYPE_1X2, $prediction->getMarketType());
        $this->assertEquals(Prediction::PREDICTION_X, $prediction->getPrediction());
        $this->assertEquals(Prediction::STATUS_UNRESOLVED, $prediction->getStatus());
        $this->assertNotEmpty($prediction->getCreatedAt());
        $this->assertNotEmpty($prediction->getUpdatedAt());
        $this->assertEquals($prediction->getCreatedAt(), $prediction->getUpdatedAt());
    }

    public function successfulChangeMarketTypeAndPredictionDataProvider(): array
    {
        $feed = [];

        $feed[] = [Prediction::MARKET_TYPE_1X2, Prediction::PREDICTION_1];
        $feed[] = [Prediction::MARKET_TYPE_1X2, Prediction::PREDICTION_2];
        $feed[] = [Prediction::MARKET_TYPE_1X2, Prediction::PREDICTION_X];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '0:0'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '0:10'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '0:1111'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1111:0'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1111:44545'];

        return $feed;
    }

    /**
     * @dataProvider successfulChangeMarketTypeAndPredictionDataProvider
     */
    public function testSuccessfulChangeMarketTypeAndPrediction(string $marketType, string $prediction)
    {
        $predictionEntity = new Prediction(1234, Prediction::MARKET_TYPE_CORRECT_SCORE, '0:0');

        $predictionEntity->changePredictionAndMarketType($prediction, $marketType);

        $this->assertEquals($marketType, $predictionEntity->getMarketType());
        $this->assertEquals($prediction, $predictionEntity->getPrediction());
    }

    public function failedToChangeMarketTypeAndPredictionDataProvider(): array
    {
        $feed = [];

        $feed[] = [Prediction::MARKET_TYPE_1X2, '0:0'];
        $feed[] = [Prediction::MARKET_TYPE_1X2, 'x'];
        $feed[] = [Prediction::MARKET_TYPE_1X2, '3'];
        $feed[] = [Prediction::MARKET_TYPE_1X2, '0'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, Prediction::PREDICTION_1];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, Prediction::PREDICTION_2];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, Prediction::PREDICTION_X];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '-1:0'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1:00'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1:09'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1:abc'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, 'abc:0'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1 9'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, '1:'];
        $feed[] = [Prediction::MARKET_TYPE_CORRECT_SCORE, ':1'];
        $feed[] = ['0:1', Prediction::MARKET_TYPE_CORRECT_SCORE];
        $feed[] = [Prediction::PREDICTION_X, Prediction::MARKET_TYPE_1X2];
        $feed[] = ['hakuna matata', Prediction::PREDICTION_X];
        $feed[] = ['hakuna matata', '1:0'];

        return $feed;
    }

    /**
     * @dataProvider failedToChangeMarketTypeAndPredictionDataProvider
     */
    public function testFailedToChangeMarketTypeAndPrediction(string $marketType, string $prediction)
    {
        $predictionEntity = new Prediction(1234, Prediction::MARKET_TYPE_CORRECT_SCORE, '0:0');

        $this->expectException(PredictionEntityException::class);
        $predictionEntity->changePredictionAndMarketType($prediction, $marketType);

    }

    public function testChangeEventId()
    {
        $prediction = new Prediction(111, Prediction::MARKET_TYPE_1X2, Prediction::PREDICTION_X);
        $prediction->changeEventId(222);

        $this->assertEquals(222, $prediction->getEventId());
    }

    public function successfulChangeStatusDataProvider(): array
    {
        $feed = [];

        $feed[] = [Prediction::STATUS_LOST];
        $feed[] = [Prediction::STATUS_WIN];

        return $feed;
    }

    /**
     * @dataProvider successfulChangeStatusDataProvider
     */
    public function testSuccessfulChangeStatus(string $status)
    {
        $predictionEntity = new Prediction(1234, Prediction::MARKET_TYPE_CORRECT_SCORE, '0:0');

        $predictionEntity->changeStatus($status);

        $this->assertEquals($status, $predictionEntity->getStatus());
    }

    public function testFailedToChangeStatus()
    {
        $predictionEntity = new Prediction(1234, Prediction::MARKET_TYPE_CORRECT_SCORE, '0:0');

        $this->expectException(PredictionEntityException::class);
        $predictionEntity->changeStatus('hakuna matata');
    }
}