<?php

namespace App\Service;

use App\Entity\Room;
use App\Helper\DTO\RoomDTO;
use App\Helper\EnumType\EntityType;
use App\Helper\Exception\ApiException;
use App\Repository\EntityTypeRepository;
use Symfony\Component\HttpFoundation\Response;

class RoomService
{
    public function __construct(
        protected EntityTypeRepository $entityTypeRepository,
    )
    {
    }

    public function addEntityToRoom(RoomDTO $roomDTO, ?Room $room = null): Room
    {
        $room = $room ?? new Room();
        if ($roomDTO->getTypeId()) {
            $entityType = $this->entityTypeRepository->find($roomDTO->getTypeId());
            if (!$entityType) {
                throw new ApiException(message: 'Не найден тип', status: Response::HTTP_NOT_FOUND);
            }
            if($entityType->getType() != EntityType::ROOM->value){
                throw new ApiException(message: 'Неверный тип');
            }
            $room->setType($entityType);
        }
        return $room;
    }
}