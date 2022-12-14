<?php

namespace App\Controller\Api;

use App\Entity\Computer;
use App\Helper\DTO\ComputerDTO;
use App\Repository\ComputerRepository;
use App\Service\ComputerService;
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
 * @OA\Tag(name="Computer")
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
#[Route('/computer')]
class ComputerApiController extends AbstractController
{
    /**
     *  Создание комьютера (Админ)
     *
     * @OA\Response(
     *     response="201",
     *     description="OK",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Computer", groups={"get_computer"})
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
     * @OA\Response(
     *     response="400",
     *     description="Not valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\RequestBody(
     *    @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\ComputerDTO", groups={"create_computer"})
     *    )
     * )
     *
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'create_computer', methods: ['POST'])]
    public function createComputer(
        Request             $request,
        ValidatorService    $validatorService,
        SerializerInterface $serializer,
        ComputerRepository  $computerRepository,
        ComputerService     $computerService,
    ): JsonResponse
    {
        /** @var ComputerDTO $computerDTO */
        $computerDTO = $serializer->deserialize(
            $request->getContent(),
            ComputerDTO::class,
            'json'
        );
        $validatorService->validate($computerDTO, ['create_computer']);
        $computer = $computerService->addEntityToComputer($computerDTO);
        $computerRepository->add($computer, true);
        return $this->json(
            data: ['data' => $computer],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_computer']]
        );
    }
    /**
     * Получение компьютера
     *
     * @OA\Response(
     *    response="200",
     *    description="Ok",
     *    @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Computer", groups={"get_computer"})
     *         )
     *    )
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     name="computer",
     *     description="id - Компьютера",
     *     @OA\Schema(type="integer")
     * )
     *
     */
    #[Route('/{computer<\d+>}', name: 'get_computer', methods: ['GET'])]
    public function getComputer(
        Computer $computer
    ): JsonResponse
    {
        return $this->json(data: [
            'data' => $computer
        ], context: ['groups' => ['get_computer']]);
    }

    /**
     * Получение списка компьютеров
     *
     * @OA\Response(
     *    response="200",
     *    description="Ok",
     *    @OA\JsonContent(
     *         @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Computer", groups={"get_computer"}))
     *         )
     *    )
     * )
     */
    #[Route('/all', name: 'get_computers', methods: ['GET'])]
    public function getAllComputer(
        ComputerRepository $computerRepository
    ): JsonResponse
    {
        return $this->json(data: [
            'data' => $computerRepository->findAll()
        ], context: ['groups' => ['get_computer']]);
    }
}