<?php

namespace App\Controller;

use App\Service\Core\Exception\RepositoryException;
use App\Service\Core\Exception\ResourceNotFoundException;
use App\Service\Core\RestRequestValidator;
use App\Service\Prediction\Exception\PredictionEntityException;
use App\Service\Prediction\PredictionManagerService;
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

    public function __construct(RestRequestValidator $restRequestValidator, PredictionManagerService $predictionManagerService)
    {
        $this->restRequestValidator = $restRequestValidator;
        $this->predictionManagerService = $predictionManagerService;
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

        $violations = $this->restRequestValidator->validateJsonContent($request->getContent(), $requestValidation);

        if (!empty($violations)) {
            //TODO log
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $content = json_decode($request->getContent(), true);

        $responseStatus = Response::HTTP_NO_CONTENT;
        try {
            $this->predictionManagerService->createPrediction($content['event_id'], $content['market_type'], $content['prediction']);
        } catch (PredictionEntityException $e) {
            //TODO log
            $responseStatus = Response::HTTP_BAD_REQUEST;
        } catch (RepositoryException | \Exception $e) {
            //TODO log
            $responseStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
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
            //TODO log
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

        $violations = $this->restRequestValidator->validateJsonContent($request->getContent(), $requestValidation);

        if (!empty($violations)) {
            //TODO log
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $content = json_decode($request->getContent(), true);

        $responseStatus = Response::HTTP_NO_CONTENT;
        try {
            $this->predictionManagerService->changeStatus($id, $content['status']);
        } catch (PredictionEntityException $e) {
            //TODO log
            $responseStatus = Response::HTTP_BAD_REQUEST;
        } catch (ResourceNotFoundException $e) {
            //TODO log
            $responseStatus = Response::HTTP_NOT_FOUND;
        } catch (RepositoryException | \Exception $e) {
            //TODO log
            $responseStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new Response(null, $responseStatus);
    }
}
