<?php

namespace App\Tests\Controller;

use App\Entity\Prediction;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PredictionControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    public $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testSuccessfulCreatePrediction()
    {
        $content = [
            'event_id' => 123,
            'market_type' => Prediction::MARKET_TYPE_1X2,
            'prediction' => Prediction::PREDICTION_2,
        ];

        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($content));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function failedToCreatePredictionBadRequestDataProvider(): array
    {
        $feed = [];

        // missing prediction field
        $feed[] = [
            [
                'event_id' => 123,
                'market_type' => Prediction::MARKET_TYPE_1X2,
            ]
        ];

        // missing market_type field
        $feed[] = [
            [
                'event_id' => 123,
                'prediction' => Prediction::PREDICTION_2,
            ]
        ];

        // missing event_id field
        $feed[] = [
            [
                'market_type' => Prediction::MARKET_TYPE_1X2,
                'prediction' => Prediction::PREDICTION_2,
            ]
        ];

        $feed[] = [
            [
                'event_id' => 123,
                'market_type' => Prediction::MARKET_TYPE_1X2,
                'prediction' => Prediction::PREDICTION_2,
                'i_should' => 'not be here'
            ]
        ];

        $feed[] = [
            [
                'event_id' => 123,
                'market_type' => 'hakuna matata',
                'prediction' => Prediction::PREDICTION_2,
            ]
        ];

        $feed[] = [
            [
                'event_id' => 123,
                'market_type' => Prediction::MARKET_TYPE_1X2,
                'prediction' => 'hakuna matata',
            ]
        ];

        $feed[] = [
            [
                'event_id' => 'hakuna matata',
                'market_type' => Prediction::MARKET_TYPE_1X2,
                'prediction' => Prediction::PREDICTION_2,
            ]
        ];

        return $feed;
    }

    /**
     * @dataProvider failedToCreatePredictionBadRequestDataProvider
     * @param array $content
     */
    public function testFailedToCreatePredictionBadRequest(array $content)
    {
        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($content));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testListAllPrediction()
    {
        $prediction1 = [
            'event_id' => 123,
            'market_type' => Prediction::MARKET_TYPE_1X2,
            'prediction' => Prediction::PREDICTION_2,
        ];
        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($prediction1));

        $prediction2 = [
            'event_id' => 356,
            'market_type' => Prediction::MARKET_TYPE_CORRECT_SCORE,
            'prediction' => '22:1',
        ];
        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($prediction2));

        $this->client->request('GET', '/v1/predictions');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->client->getResponse()->getContent();
        $response = json_decode($response, true);

        $this->assertCount(2, $response);

        $this->assertIsInt($response[0]['id']);
        $this->assertSame($prediction1['event_id'], $response[0]['evenet_id']);
        $this->assertSame($prediction1['market_type'], $response[0]['market_type']);
        $this->assertSame($prediction1['prediction'], $response[0]['prediction']);
        $this->assertSame(Prediction::STATUS_UNRESOLVED, $response[0]['status']);

        $this->assertIsInt($response[1]['id']);
        $this->assertSame($prediction2['event_id'], $response[1]['evenet_id']);
        $this->assertSame($prediction2['market_type'], $response[1]['market_type']);
        $this->assertSame($prediction2['prediction'], $response[1]['prediction']);
        $this->assertSame(Prediction::STATUS_UNRESOLVED, $response[1]['status']);
    }

    public function testSuccessfulUpdatePredictionStatus()
    {
        $create = [
            'event_id' => 123,
            'market_type' => Prediction::MARKET_TYPE_1X2,
            'prediction' => Prediction::PREDICTION_2,
        ];
        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($create));

        $this->client->request('GET', '/v1/predictions');
        $response = $this->client->getResponse()->getContent();
        $response = json_decode($response, true);

        $id = $response[0]['id'];

        $update = [
            'status' => Prediction::STATUS_LOST,
        ];
        $this->client->request('PUT', "/v1/predictions/$id/status", [], [], [], json_encode($update));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/v1/predictions');
        $response = $this->client->getResponse()->getContent();
        $response = json_decode($response, true);

        $this->assertSame($response[0]['status'], Prediction::STATUS_LOST);
    }

    public function testFailedTolUpdatePredictionStatusInvalidStatus()
    {
        $create = [
            'event_id' => 1233,
            'market_type' => Prediction::MARKET_TYPE_1X2,
            'prediction' => Prediction::PREDICTION_1,
        ];
        $this->client->request('POST', '/v1/predictions', [], [], [], json_encode($create));

        $this->client->request('GET', '/v1/predictions');
        $response = $this->client->getResponse()->getContent();
        $response = json_decode($response, true);

        $id = $response[0]['id'];

        $update = [
            'status' => 'hakuna matata',
        ];
        $this->client->request('PUT', "/v1/predictions/$id/status", [], [], [], json_encode($update));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testFailedTolUpdatePredictionStatusPredictionNotFound()
    {
        $id = 1500100900;
        $update = [
            'status' => Prediction::STATUS_WIN,
        ];
        $this->client->request('PUT', "/v1/predictions/$id/status", [], [], [], json_encode($update));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}