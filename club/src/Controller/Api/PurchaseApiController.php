<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helper\EnumStatus\ScheduleStatus;
use App\Helper\EnumType\PurchaseType;
use App\Helper\Exception\ApiException;
use App\Service\ScheduleService;
use App\Service\ValidatorService;
use Doctrine\Common\Collections\Criteria;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Purchase")
 *
 * @OA\Response(
 *     response="401",
 *     description="Unauthorized",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 */
#[Route('/purchase')]
class PurchaseApiController extends AbstractController
{
    /**
     * Оплата заказа
     *
     * @OA\Response(
     *     response="204",
     *     description="Success"
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     required=true,
     *     name="type",
     *     description="Тип заказа (1 - Бронь компьютера)",
     *     @OA\Schema(type="integer", enum={1})
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     name="entityId",
     *     required=true,
     *     description="id - заказа",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Not valid type",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found order",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/type/{type}/order/{entityId}', name: 'purchase', methods: ["POST"])]
    public function purchase(
        int|string       $type,
        int|string       $entityId,
        ValidatorService $validatorService,
        ScheduleService  $scheduleService,
    ): JsonResponse
    {
        $validatorService->validateMaxSizeInteger([$type, $entityId]);
        if (!in_array($type, PurchaseType::getTypes())) {
            throw new ApiException(message: 'Неверный тип');
        }
        if ($type == PurchaseType::SCHEDULE->value) {
            $scheduleService->paySchedule(
                $entityId,
                ScheduleStatus::WAIT_PAYMENT->value,
                $this->getUser()
            );
        }

        return $this->json(
            data: [],
            status: Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Получение заказов пользователя
     *
     * @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Schedule", groups={"get_schedule"}))
     *          )
     *     )
     * )
     *
     */
    #[Route('', name: 'get_user_purchases', methods: ['GET'])]
    public function getPurchases(): JsonResponse
    {
        $schedules =$this->getUser()->getSchedules();
        $sort = Criteria::create()->orderBy(['createdAt' => 'DESC']);
        return $this->json(data: [
            'data' => $schedules->matching($sort)
        ], context: ['groups' => ['get_schedule']]);
    }
}