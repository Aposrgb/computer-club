<?php

namespace App\Service;

use App\Entity\Computer;
use App\Entity\Schedule;
use App\Entity\User;
use App\Helper\EnumStatus\ScheduleStatus;
use App\Helper\Exception\ApiException;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\Response;
use function Webmozart\Assert\Tests\StaticAnalysis\contains;

class ScheduleService
{
    public function __construct(
        protected ScheduleRepository $scheduleRepository
    )
    {
    }

    /** @param Schedule[] $schedules */
    public function checkTimeSchedulesUser(array $schedules, Schedule $newSchedule): void
    {
        $newDateStart = $newSchedule->getDateStart();
        $newEndStart = (clone $newDateStart)->modify($newSchedule->getHours() . ' hours');
        $schedules = array_filter($schedules, function ($item) {
            return !($item->getStatus() == ScheduleStatus::ARCHIVE->value or
                $item->getStatus() == ScheduleStatus::CANCELLED->value);
        });
        $this->checkTimeSchedules($newDateStart, $newEndStart, $schedules);
    }

    public function getScheduleByStatusAndUser(int $id, ?int $status, ?User $user): Schedule
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
        return $schedule;
    }

    public function checkTimeSchedule(\DateTimeInterface $date, int $hours, int $computerId): void
    {
        $endDay = (clone $date)->modify($hours . ' hours');
        $schedules = $this->scheduleRepository->findScheduleByDay($endDay, $computerId);
        $this->checkTimeSchedules($date, $endDay, $schedules);
    }

    private function checkTimeSchedules(\DateTimeInterface $newDateStart, \DateTimeInterface $newEndStart, array $schedules): void
    {
        foreach ($schedules as $schedule) {
            $dateStartSession = $schedule->getDateStart();
            $dateEndSession = (clone $dateStartSession)->modify($schedule->getHours() . ' hours');
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
                $dateEndSession = (clone $dateStartSession)->modify($schedule->getHours() . ' hours');
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