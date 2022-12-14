<?php

namespace App\Service;

use App\Entity\Computer;
use App\Entity\Schedule;
use App\Entity\User;
use App\Helper\EnumStatus\ScheduleStatus;
use App\Helper\Exception\ApiException;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ScheduleService
{
    public function __construct(
        protected ScheduleRepository $scheduleRepository,
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function setFullPriceToSchedule(Schedule $schedule): Schedule
    {
        $interval = $schedule->getDateStart()->diff($schedule->getDateEnd());
        $hours = $interval->h > 0 ? $interval->h : 1;
        $minutes = $interval->i;
        $price = (int)($schedule->getPrice() * $hours + $schedule->getPrice() *  $minutes/60);
        return $schedule->setPrice($price);
    }

    /** @param Schedule[] $schedules */
    public function checkTimeSchedulesUser(array $schedules, Schedule $newSchedule): void
    {
        $newDateStart = $newSchedule->getDateStart();
        $newEndStart = $newSchedule->getDateEnd();
        $schedules = array_filter($schedules, function ($item) {
            return !($item->getStatus() == ScheduleStatus::ARCHIVE->value or
                $item->getStatus() == ScheduleStatus::CANCELLED->value);
        });
        $this->checkTimeSchedules($newDateStart, $newEndStart, $schedules);
    }

    public function paySchedule(int $id, ?int $status, ?User $user): void
    {
        $query = ['id' => $id];
        if ($status) {
            $query['status'] = $status;
        }
        if ($user) {
            $query['owner'] = $user;
        }
        $schedule = $this->scheduleRepository->findOneBy($query);
        if (!$schedule) {
            throw new ApiException(
                message: "Не найдено расписание",
                status: Response::HTTP_NOT_FOUND
            );
        }
        $schedule->setStatus(ScheduleStatus::ACTIVE->value);
        $this->entityManager->flush();
    }

    public function checkTimeSchedule(\DateTimeInterface $date, \DateTimeInterface $endDay, int $computerId): void
    {
        $schedules = $this->scheduleRepository->findScheduleByDay($date, $endDay, $computerId);
        $this->checkTimeSchedules($date, $endDay, $schedules);
    }

    private function checkTimeSchedules(\DateTimeInterface $newDateStart, \DateTimeInterface $newEndStart, array $schedules): void
    {
        /** @var Schedule $schedule */
        foreach ($schedules as $schedule) {
            $dateStartSession = $schedule->getDateStart();
            $dateEndSession = $schedule->getDateEnd();
            if ($newDateStart >= $dateStartSession && $newDateStart <= $dateEndSession) {
                throw new ApiException(message: 'В данное время комьютер занят');
            }
            if ($newEndStart <= $dateEndSession && $newEndStart >= $dateStartSession) {
                throw new ApiException(message: 'В данное время комьютер занят');
            }
            if ($newDateStart <= $dateStartSession && $newEndStart >= $dateEndSession) {
                throw new ApiException(message: 'В данное время комьютер занят');
            }
        }
    }
    /** @param Schedule[] $schedules */
    public function checkTimeDateComputer(\DateTimeInterface $newDateStart, \DateTimeInterface $newEndStart, array $schedules, Computer $computer): bool
    {
        foreach ($schedules as $schedule) {
            if ($schedule->getComputer()->getId() == $computer->getId()) {
                $dateStartSession = $schedule->getDateStart();
                $dateEndSession = $schedule->getDateEnd();
                if ($newDateStart >= $dateStartSession && $newDateStart <= $dateEndSession) {
                    return false;
                }
                if ($newEndStart <= $dateEndSession && $newEndStart >= $dateStartSession) {
                    return false;
                }
                if ($newDateStart <= $dateStartSession && $newEndStart >= $dateEndSession) {
                    return false;
                }
            }
        }
        return true;
    }
}