<?php

namespace App\Controller\Api;

use App\Service\ExampleService;
use App\Service\ValidatorService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Example")
 */
class ExampleApiController extends AbstractController
{
    /**
     * Начальная страница
     *
     * @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="Страница - 1 по умолчанию",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="limit",
     *     description="Ограничение по кол-ву",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Not valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(
        ExampleService      $exampleService,
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService
    ): JsonResponse
    {
        $query = $request->query->all();
        $pagination = $serializer
            ->deserialize(
                json_encode($query), \PaginationFilter::class, 'json'
            );
        $validatorService->validate(body: $pagination, groupsBody: ['pagination']);
        $exampleService->exampleMethod();

        return $this->json(data: $pagination, status: Response::HTTP_OK, context: []);
    }
}