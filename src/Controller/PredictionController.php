<?php

namespace App\Controller;

use App\Service\Core\Exception\RepositoryException;
use App\Service\Core\Exception\ResourceNotFoundException;
use App\Service\Core\RestRequestValidator;
use App\Service\Prediction\Exception\PredictionEntityException;
use App\Service\Prediction\PredictionManagerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class PredictionController
{
    /** @var RestRequestValidator */
    private $restRequestValidator;

    /** @var PredictionManagerService */
    private $predictionManagerService;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(RestRequestValidator $restRequestValidator, PredictionManagerService $predictionManagerService, LoggerInterface $logger)
    {
        $this->restRequestValidator = $restRequestValidator;
        $this->predictionManagerService = $predictionManagerService;
        $this->logger = $logger;
    }

    /** @Route("/v1/predictions", name="create_prediction", methods={"POST"}) */
    public function create(Request $request): Response
    {
        $requestValidation = new Assert\Collection(
            [
                'event_id' => [new Assert\Type('integer'), new Assert\NotBlank()],
                'market_type' => [new Assert\Type('string'), new Assert\NotBlank()],
                'prediction' => [new Assert\Type('string'), new Assert\NotBlank()],
            ]
        );

        $requestContent = $request->getContent();
        $violations = $this->restRequestValidator->validateJsonContent($requestContent, $requestValidation);

        if (!empty($violations)) {
            $this->logger->error(
                'Invalid request content.',
                [
                    'route' => 'create_prediction',
                    'content' => $requestContent,
                    'violations' => $violations,
                ]
            );
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $content = json_decode($requestContent, true);

        $responseStatus = Response::HTTP_NO_CONTENT;
        $exception = null;
        try {
            $this->predictionManagerService->createPrediction($content['event_id'], $content['market_type'], $content['prediction']);
        } catch (PredictionEntityException $e) {
            $exception = $e;
            $responseStatus = Response::HTTP_BAD_REQUEST;
        } catch (RepositoryException | \Exception $e) {
            $exception = $e;
            $responseStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if ($exception) {
            $this->logger->error(
                'Failed to update Prediction.',
                [
                    'route' => 'update_prediction',
                    'content' => $requestContent,
                    'exception' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );
        }

        return new Response(null, $responseStatus);
    }

    /** @Route("/v1/predictions", name="list_all_predictions", methods={"GET"}) */
    public function listAll(Request $request): JsonResponse
    {
        $responseStatus = Response::HTTP_OK;
        $predictions = null;
        try {
            $predictions = $this->predictionManagerService->list();
        } catch (RepositoryException | \Exception $e) {
            $this->logger->error(
                'Failed to list Predictions.',
                [
                    'route' => 'list_all_predictions',
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            $responseStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($predictions, $responseStatus);
    }

    /** @Route("/v1/predictions/{id}/status", name="update_prediction", methods={"PUT"}, requirements={"id"="\d+"}) */
    public function update(Request $request, $id): Response
    {
        $requestValidation = new Assert\Collection(
            [
                'status' => [new Assert\Type('string'), new Assert\NotBlank()],
            ]
        );

        $requestContent = $request->getContent();
        $violations = $this->restRequestValidator->validateJsonContent($requestContent, $requestValidation);

        if (!empty($violations)) {
            $this->logger->error(
                'Invalid request content.',
                [
                    'route' => 'update_prediction',
                    'content' => $requestContent,
                    'violations' => $violations,
                ]
            );
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $content = json_decode($requestContent, true);

        $responseStatus = Response::HTTP_NO_CONTENT;
        $exception = null;
        try {
            $this->predictionManagerService->changeStatus($id, $content['status']);
        } catch (PredictionEntityException $e) {
            $exception = $e;
            $responseStatus = Response::HTTP_BAD_REQUEST;
        } catch (ResourceNotFoundException $e) {
            $exception = $e;
            $responseStatus = Response::HTTP_NOT_FOUND;
        } catch (RepositoryException | \Exception $e) {
            $exception = $e;
            $responseStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if ($exception) {
            $this->logger->error(
                'Failed to update Prediction.',
                [
                    'route' => 'update_prediction',
                    'content' => $requestContent,
                    'exception' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );
        }

        return new Response(null, $responseStatus);
    }
}
