<?php

namespace App\Command;

use App\Entity\Example;
use App\Repository\ExampleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ExampleCommand extends Command
{
    public function __construct(
        protected ExampleRepository      $exampleRepository,
        protected EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('example:entity:insert')
            ->setDescription('Insert entity with name in db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $question = (new Question('<question>Please set name</question>'));

        $helper = $this->getHelper('question');
        $name = $helper->ask($input, $output, $question);

        $question = (new Question('<question>Please set age</question>'));
        $age = $helper->ask($input, $output, $question);

        $exampleEntity = (new Example())
            ->setName($name)
            ->setAges($age);

        $this->exampleRepository->add($exampleEntity);
        /** Сверху и снизу, одно и то же  */
        $this->entityManager->persist($exampleEntity);
        /**
         * Он записывает, что нужно потом insert-ить в бд
         * Также генерит id для всех сущностей
         */

        /** Получить репозиторий также можно и через entityManager */
        $exampleRepository = $this->entityManager->getRepository(Example::class);

        /**
         * Дальше обновляем бд
         */
        $this->entityManager->flush();
        /**
         * Данные теперь находятся в бд
         */
        /**
         *  Также можно воспользоваться более короткой записью через репозиторий
         */
        $exampleRepository->add($exampleEntity, true); // После этого все будет в бд
        $exampleRepository->add($exampleEntity); // Без параметра не произойдет entityManager->flush()
        return Command::SUCCESS;
    }
}