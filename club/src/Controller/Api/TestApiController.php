<?php

namespace App\Controller\Api;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @OA\Tag(name="Test") */
#[Route('/test')]
class TestApiController extends AbstractController
{
    /**
     * Метод для проверки авторизации
     *
     * Если 4xx Ошибка авторизация не пройдена
     */
    #[Route('', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(
            data: ['data' => 'test'],
            status: Response::HTTP_OK
        );
    }
}