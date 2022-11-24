<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helper\DTO\ScheduleDTO;
use App\Helper\Exception\ApiException;
use App\Helper\Mapper\ScheduleMapper;
use App\Repository\ScheduleRepository;
use App\Service\ComputerService;
use App\Service\ScheduleService;
use App\Service\UserService;
use App\Service\ValidatorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Schedule")
 */
#[Route('/schedule')]
class ScheduleApiController extends AbstractController
{
    /**
     * Получить расписание на неделю
     *
     * @OA\Parameter(
     *     in="query",
     *     name="offset",
     *     description="offset = 1 следущая неделя",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Schedule", groups={"get_schedule"}))
     *          )
     *     )
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Offset should be int",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/week', name: 'get_schedules_week', methods: ['GET'])]
    public function getSchedulesCurrentWeek(
        Request            $request,
        ScheduleRepository $scheduleRepository
    ): JsonResponse
    {
        $week = $request->query->get('offset', 0);
        $date = (new \DateTime())
            ->modify('monday this week')
            ->modify($week . ' week')
            ->setTime(0, 0);
        return $this->json(
            data: ['data' =>
                $scheduleRepository->findScheduleWeek($date)
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_schedule']]
        );
    }

    /**
     * Бронирование компьютера
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *          ref=@Model(type="App\Helper\DTO\ScheduleDTO", groups={"create_schedule"})
     *     )
     * )
     *
     * @OA\Response(
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Schedule", groups={"get_schedule"})
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="No valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="401",
     *     description="Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found user or computer",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/week', name: 'create_schedule', methods: ['POST'])]
    public function createSchedule(
        Request             $request,
        ValidatorService    $validatorService,
        SerializerInterface $serializer,
        ComputerService     $computerService,
        ScheduleService     $scheduleService,
        ScheduleMapper      $scheduleMapper,
        ScheduleRepository  $scheduleRepository
    ): JsonResponse
    {
        /** @var ScheduleDTO $scheduleDTO */
        $scheduleDTO = $serializer->deserialize(
            $request->getContent(),
            ScheduleDTO::class,
            'json'
        );
        $validatorService->validate($scheduleDTO, ['create_schedule']);
        $schedule = $scheduleMapper->dtoToEntity($scheduleDTO);
        /** @var User $user */
        $user = $this->getUser();
        $schedule
            ->setOwner($user)
            ->setComputer(
                $computerService->getComputerById($scheduleDTO->getComputerId())
            );
        if ($schedule->getDateStart() < new \DateTime()) {
            throw new ApiException(message: 'Нельзя забронировать');
        }
        $scheduleService->checkTimeSchedule(
            date: $schedule->getDateStart(),
            hours: $schedule->getHours(),
            computerId: $schedule->getComputer()->getId()
        );
        $scheduleService->checkTimeSchedulesUser($user->getSchedules()->toArray(), $schedule);
        $scheduleRepository->add($schedule, true);
        return $this->json(
            data: ['data' => $schedule],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_schedule']]
        );
    }
}