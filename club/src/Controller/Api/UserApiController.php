<?php

namespace App\Controller\Api;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="User")
 */
#[Route('/user')]
class UserApiController extends AbstractController
{
    /**
     * Получение профиля текущего пользователя
     *
     * @OA\Response(
     *     response="401",
     *     description="Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="OK",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\User", groups={"get_user"})
     *          )
     *     )
     * )
     *
     *
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('', name: 'get_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        return $this->json(data: [
            'data' => $this->getUser()
        ], context: ['groups' => ['get_user']]);
    }
}