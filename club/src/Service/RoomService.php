<?php

namespace App\Service;

use App\Entity\Room;
use App\Helper\DTO\RoomDTO;
use App\Helper\EnumStatus\ComputerStatus;
use App\Helper\EnumStatus\ScheduleStatus;
use App\Helper\EnumType\EntityType;
use App\Helper\Exception\ApiException;
use App\Helper\Filter\RoomFilter;
use App\Helper\Mapped\Computer as ComputerMapped;
use App\Helper\Mapped\Room as RoomMapped;
use App\Repository\EntityTypeRepository;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\Response;

class RoomService
{
    public function __construct(
        protected EntityTypeRepository $entityTypeRepository,
        protected ScheduleRepository   $scheduleRepository,
        protected ScheduleService      $scheduleService,
        protected ComputerService      $computerService,
    )
    {
    }

    /** @return RoomMapped[] */
    public function getRoomMappedWithFilter(RoomFilter $roomFilter, array $rooms): array
    {
        $roomsMapped = [];
        if (!$roomFilter->getStartDate()) {
            throw new ApiException(message: 'Нет даты начала для проверки');
        }
        $roomFilter->setStartDate(new \DateTime($roomFilter->getStartDate()));
        if (!$roomFilter->getEndDate()) {
            throw new ApiException(message: 'Нет даты конца для проверки');
        }
        $roomFilter->setEndDate(new \DateTime($roomFilter->getEndDate()));
        $this->validateDateFilter($roomFilter->getStartDate(), $roomFilter->getEndDate());
        $schedules = $this->scheduleRepository->findBy([
            'status' => [ScheduleStatus::ACTIVE->value, ScheduleStatus::WAIT_PAYMENT->value]
        ]);
        foreach ($rooms as $room) {
            $computers = [];
            foreach ($room->getComputers() as $computer) {
                $status = ComputerStatus::ARCHIVE->value;
                if ($this->scheduleService
                    ->checkTimeDateComputer(
                        $roomFilter->getStartDate(), $roomFilter->getEndDate(), $schedules, $computer
                    )) {
                    $status = ComputerStatus::ACTIVE->value;
                }
                $computers[] = (new ComputerMapped())
                    ->setDescription($computer->getType()?->getDescription())
                    ->setId($computer->getId())
                    ->setPrice($computer->getType()?->getPrice())
                    ->setStatus($status);
            }
            if (empty($computers)) {
                continue;
            }
            $roomsMapped[] = (new RoomMapped())
                ->setPrice($room->getType()?->getPrice())
                ->setId($room->getId())
                ->setDescription($room->getType()?->getDescription())
                ->setComputers($computers);
        }
        return $roomsMapped;
    }

    public function validateDateFilter(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        if ($startDate <= new \DateTime()) {
            throw new ApiException('Неверное время');
        }
        if ($startDate >= $endDate) {
            throw new ApiException('Не валидное время');
        }
    }

    /** @return RoomMapped[] */
    public function getRoomMapped(array $rooms): array
    {
        $roomsMapped = [];
        foreach ($rooms as $room) {
            $computers = [];
            foreach ($room->getComputers() as $computer) {
                $computers[] = (new ComputerMapped())
                    ->setDescription($computer->getType()?->getDescription())
                    ->setId($computer->getId())
                    ->setPrice($computer->getType()?->getPrice())
                    ->setStatus(ComputerStatus::ACTIVE->value);
            }
            if (empty($computers)) {
                continue;
            }
            $roomsMapped[] = (new RoomMapped())
                ->setId($room->getId())
                ->setDescription($room->getType()?->getDescription())
                ->setPrice($room->getType()?->getPrice())
                ->setComputers($computers);
        }
        return $roomsMapped;
    }

    public function addEntityToRoom(RoomDTO $roomDTO, ?Room $room = null): Room
    {
        $room = $room ?? new Room();
        if ($roomDTO->getTypeId()) {
            $entityType = $this->entityTypeRepository->find($roomDTO->getTypeId());
            if (!$entityType) {
                throw new ApiException(message: 'Не найден тип', status: Response::HTTP_NOT_FOUND);
            }
            if ($entityType->getType() != EntityType::ROOM->value) {
                throw new ApiException(message: 'Неверный тип');
            }
            $room->setType($entityType);
        }
        return $room;
    }
}