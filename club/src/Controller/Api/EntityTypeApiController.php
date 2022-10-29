<?php

namespace App\Controller\Api;

use App\Helper\DTO\EntityTypeDTO;
use App\Helper\Exception\ApiException;
use App\Helper\Mapper\EntityTypeMapper;
use App\Repository\EntityTypeRepository;
use App\Service\EntityTypeService;
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
 * @OA\Tag(name="Type")
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
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/type')]
class EntityTypeApiController extends AbstractController
{
    /**
     * Создание типа
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *              ref=@Model(type="App\Helper\DTO\EntityTypeDTO", groups={"create_type"})
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response="201",
     *     description="success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\EntityType", groups={"get_type"})
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
    #[Route('', name: 'create_type', methods: ['POST'])]
    public function createType(
        Request              $request,
        ValidatorService     $validatorService,
        EntityTypeMapper     $mapper,
        EntityTypeRepository $entityTypeRepository,
        SerializerInterface  $serializer,
        EntityTypeService    $service,
    ): JsonResponse
    {
        $typeDTO = $serializer->deserialize(
            json_encode($request->request->all()),
            EntityTypeDTO::class,
            'json'
        );
        $validatorService->validate($typeDTO, ['create_type']);
        $files = $request->files->all();
        if (count($files) > EntityTypeService::COUNT_FILES_IN_TYPE) {
            throw new ApiException(message: 'Превышен лимит файлов для типа');
        }
        $validatorService->validateImagesExtension($files, EntityTypeService::FILE_EXTENSION_AVAILABLE);
        $type = $mapper->dtoToEntity($typeDTO);
        $type = $service->uploadFiles($type, $files);
        $entityTypeRepository->add($type, true);
        return $this->json(
            data: ['data' => $type],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_type']]
        );
    }
}