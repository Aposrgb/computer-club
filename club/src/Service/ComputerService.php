<?php

namespace App\Service;

use App\Entity\Computer;
use App\Helper\DTO\ComputerDTO;
use App\Helper\EnumType\EntityType;
use App\Helper\Exception\ApiException;
use App\Repository\ComputerRepository;
use App\Repository\EntityTypeRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Response;

class ComputerService
{
    public function __construct(
        protected ComputerRepository   $computerRepository,
        protected RoomRepository       $roomRepository,
        protected EntityTypeRepository $entityTypeRepository,
    )
    {
    }

    public function addEntityToComputer(ComputerDTO $computerDTO, ?Computer $computer = null): Computer
    {
        $computer = $computer ?? new Computer();
        if ($computerDTO->getRoomId()) {
            $room = $this->roomRepository->find($computerDTO->getRoomId());
            if (!$room) {
                throw new ApiException(message: 'Не найдена комната', status: Response::HTTP_NOT_FOUND);
            }
            if (!$room->getType()) {
                throw new ApiException(message: 'Нельзя добавить, у комнаты нет типа');
            }
            $computer->setRoom($room);
        }
        if ($computerDTO->getTypeId()) {
            $entityType = $this->entityTypeRepository->find($computerDTO->getTypeId());
            if (!$entityType) {
                throw new ApiException(message: 'Не найден тип', status: Response::HTTP_NOT_FOUND);
            }
            if ($entityType->getType() != EntityType::COMPUTER->value) {
                throw new ApiException(message: 'Неверный тип');
            }
            $computer->setType($entityType);
        }
        return $computer;
    }

    public function getComputerById(int $id): Computer
    {
        $computer = $this->computerRepository->find($id);
        if (!$id) {
            throw new ApiException(message: 'Не найден компьютер', status: Response::HTTP_NOT_FOUND);
        }
        return $computer;
    }

}