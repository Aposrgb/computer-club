<?php

namespace App\Controller\Api;

use App\Entity\Device;
use App\Helper\DTO\UserDTO;
use App\Helper\Mapper\UserMapper;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Service\ValidatorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/** @OA\Tag(name="Security") */
#[Route('/security')]
class SecurityApiController extends AbstractController
{
    /**
     * Регистрация
     *
     * @OA\Response(
     *     response="400",
     *     description="No valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *          ref=@Model(type="App\Helper\DTO\UserDTO", groups={"registration"})
     *     )
     * )
     * @OA\Response(
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              @OA\Property(property="token", type="string")
     *          )
     *     )
     * )
     */
    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function registration(
        Request                     $request,
        SerializerInterface         $serializer,
        UserService                 $userService,
        ValidatorService            $validatorService,
        UserMapper                  $userMapper,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $hasher,
    ): JsonResponse
    {
        /** @var UserDTO $userDTO */
        $userDTO = $serializer->deserialize(
            $request->getContent(), UserDTO::class, 'json'
        );
        $validatorService->validate(body: $userDTO, groupsBody: ['registration']);
        $userService->checkEmailExist($userDTO->getEmail());
        $user = $userMapper->dtoToEntity($userDTO);
        $user
            ->setPassword($hasher->hashPassword($user, $userDTO->getPassword()))
            ->addDevice($device = new Device());
        $userRepository->add($user, true);
        return $this->json(
            data: ['data' => [
                'token' => $device->getToken()
            ]],
            status: Response::HTTP_CREATED
        );
    }
}