<?php

namespace App\Controller\Api;

use App\Helper\DTO\RoomDTO;
use App\Helper\Mapper\RoomMapper;
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
        RoomMapper          $roomMapper,
    ): JsonResponse
    {
        $roomDTO = $serializer->deserialize(
            $request->getContent(),
            RoomDTO::class,
            'json'
        );
        $validatorService->validate($roomDTO, ['create_room']);
        $room = $roomMapper->dtoToEntity($roomDTO);
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
     *              ref=@Model(type="App\Entity\Room", groups={"get_room"})
     *          )
     *     )
     * )
     */
    #[Route('', name: 'get_rooms', methods: ["GET"])]
    public function getRooms(
        RoomRepository $repository,
    ): JsonResponse
    {
        return $this->json(data: ['data' => $repository->findAll()], context: ['groups'=> ['get_room']]);
    }
}