<?php

namespace App\Command;

use App\Helper\EnumStatus\ScheduleStatus;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckScheduleDateCommand extends Command
{
    public function __construct(
        protected ScheduleRepository     $scheduleRepository,
        protected EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('schedule:check:date')
            ->setDescription('Check date schedule and update status schedule');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $schedulesActive = $this->scheduleRepository->findBy([
//            'status' => ScheduleStatus::ACTIVE->value
//        ]);
//
//        foreach ($schedulesActive as $schedule) {
//            if ($schedule->getDateEnd() < new \DateTime()) {
//                $schedule->setStatus(ScheduleStatus::ARCHIVE->value);
//            }
//        }
//
//        $schedulesWaitPayment = $this->scheduleRepository->findBy([
//            'status' => ScheduleStatus::WAIT_PAYMENT->value
//        ]);
//
//        $date = (new \DateTime())->modify("-20 minutes");
//        foreach ($schedulesWaitPayment as $schedule) {
//            if ($schedule->getCreatedAt() < $date) {
//                $schedule->setStatus(ScheduleStatus::CANCELLED->value);
//            }
//        }

//        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}