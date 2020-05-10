<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController
{
    /** @Route("/predictions", name="create_prediction", methods={"POST"}) */
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_OK);
    }

    /** @Route("/predictions", name="list_all_predictions", methods={"GET"}) */
    public function listAll(Request $request): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_OK);
    }

    /** @Route("/predictions/{id}/{status}", name="update_prediction", methods={"PUT"}, requirements={"id"="\d+"}) */
    public function update(Request $request, $id, $status): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_OK);
    }
}