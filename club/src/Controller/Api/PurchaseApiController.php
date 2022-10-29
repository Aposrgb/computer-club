<?php

namespace App\Controller\Api;

use App\Helper\EnumStatus\ScheduleStatus;
use App\Helper\EnumType\PurchaseType;
use App\Helper\Exception\ApiException;
use App\Repository\ScheduleRepository;
use App\Service\ScheduleService;
use App\Service\ValidatorService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

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
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              @OA\Property(property="link", type="string")
     *          )
     *     )
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
    #[Route('/type/{type}/order/{entityId}', name: 'purchase', methods: ["GET"])]
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
        $schedule = null;
        if ($type == PurchaseType::SCHEDULE->value) {
            $schedule = $scheduleService->getScheduleByStatusAndUser(
                $entityId,
                ScheduleStatus::WAIT_PAYMENT->value,
                $this->getUser()
            );
        }

        return $this->json(
            data: ['data' => [
                'price' => $schedule?->getPrice(),
                'dateStart' => $schedule?->getDateStart(),
                'computer' => $schedule?->getComputer()
            ]]
        );
    }
}