<?php

namespace App\Service;

use App\Entity\Schedule;
use App\Entity\User;
use App\Helper\Exception\ApiException;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\Response;

class ScheduleService
{
    public function __construct(
        protected ScheduleRepository $scheduleRepository
    )
    {
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
        foreach ($schedules as $schedule) {
            $dateStartSession = $schedule->getDateStart();
            $dateEndSession = (clone $dateStartSession)->modify($schedule->getHours() . ' hours');
            if ($date > $dateStartSession && $date < $dateEndSession) {
                throw new ApiException(message: 'В данное время комьютер занят');
            }
            if ($endDay < $dateEndSession && $endDay > $dateStartSession) {
                throw new ApiException(message: 'В данное время комьютер занят');
            }
        }
    }
}