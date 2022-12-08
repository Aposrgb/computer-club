<?php

namespace App\Controller\Api;

use App\Entity\Room;
use App\Helper\DTO\RoomDTO;
use App\Helper\Filter\RoomFilter;
use App\Repository\RoomRepository;
use App\Service\RoomService;
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
 * @OA\Tag(name="Room")
 *
 * @OA\Response(
 *     response="401",
 *     description="Unauthorized",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 *
 * @OA\Response(
 *     response="403",
 *     description="Assecc denied",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 */
#[Route('/room')]
class RoomApiController extends AbstractController
{
    /**
     * Создание комнаты (Админ)
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\RoomDTO", groups={"create_room"})
     *     )
     * )
     *
     * @OA\Response(
     *     response="201",
     *     description="success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Room", groups={"get_room"})
     *          )
     *     )
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('', name: 'create_room', methods: ['POST'])]
    public function createRoom(
        Request             $request,
        ValidatorService    $validatorService,
        RoomRepository      $roomRepository,
        SerializerInterface $serializer,
        RoomService         $service,
    ): JsonResponse
    {
        $roomDTO = $serializer->deserialize(
            $request->getContent(),
            RoomDTO::class,
            'json'
        );
        $validatorService->validate($roomDTO, ['create_room']);
        $room = new Room();
        $room = $service->addEntityToRoom($roomDTO, $room);
        $roomRepository->add($room, true);
        return $this->json(
            data: ['data' => $room],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_room']]
        );
    }

    /**
     * Получение всех комнат
     *
     * @OA\Response(
     *     response="200",
     *     description="success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Helper\Mapped\Room")
     *          )
     *     )
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="search[startDate]",
     *     @OA\Schema(type="string", example="2022-12-12 12:00")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="search[endDate]",
     *     @OA\Schema(type="string", example="2022-12-12 16:00")
     * )
     *
     */
    #[Route('', name: 'get_rooms', methods: ["GET"])]
    public function getRooms(
        Request             $request,
        SerializerInterface $serializer,
        RoomRepository      $roomRepository,
        ValidatorService    $validatorService,
        RoomService         $roomService,
    ): JsonResponse
    {
        $query = $request->query->all();
        /** @var RoomFilter $roomFilter */
        $roomFilter = $serializer->deserialize(json_encode($query['search'] ?? []), RoomFilter::class, 'json');
        $validatorService->validate($roomFilter, ['filter']);
        $rooms = $roomRepository->findAll();

        if (!$roomFilter->getStartDate() && !$roomFilter->getEndDate()) {
            $roomsMapped = $roomService->getRoomMapped($rooms);
        } else {
            $roomsMapped = $roomService->getRoomMappedWithFilter($roomFilter, $rooms);
        }

        return $this->json(data: ['data' => $roomsMapped]);
    }
    /**
     * Получение комнаты
     *
     * @OA\Parameter(
     *     in="path",
     *     name="room",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Room", groups={"get_room"})
     *          )
     *     )
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/{room}', name: 'get_api_room', methods: ["GET"])]
    public function getRoom(Room $room): JsonResponse
    {
        return $this->json(['data' => $room], context: ['groups' => ['get_room']]);
    }
}