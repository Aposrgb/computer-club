<?php

namespace App\Command;

use App\Entity\User;
use App\Helper\EnumStatus\UserStatus;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected UserRepository              $userRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('admin:create')
            ->setDescription('Check date schedule and update status schedule');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = (new User())
            ->setRoles(["ROLE_ADMIN"])
            ->setStatus(UserStatus::ACTIVE->value);

        $helper = $this->getHelper('question');

        $question = new Question(
            '<question>Please type username</question>: ',
        );
        $user->setName($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type surname</question>: ',
        );
        $user->setSurname($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type email</question>: ',
        );
        $user->setEmail($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type password</question>: ',
        );
        $user->setPassword($this->hasher->hashPassword($user, $helper->ask($input, $output, $question)));


        $this->userRepository->add($user, true);
        return Command::SUCCESS;

    }
}