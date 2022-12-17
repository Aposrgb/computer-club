<?php

namespace App\Controller\Admin;

use App\Entity\Computer;
use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Repository\ComputerRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/schedule')]
class ScheduleController extends AbstractController
{
    #[Route('/', name: 'app_schedule_index', methods: ['GET'])]
    public function index(ScheduleRepository $scheduleRepository): Response
    {
        return $this->render('schedule/index.html.twig', [
            'schedules' => $scheduleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_schedule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ValidatorInterface $validator,ComputerRepository $computerRepository,UserRepository $userRepository,  EntityManagerInterface $entityManager): Response
    {
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($schedule->getDateStart() < new \DateTime()) {
                return $this->renderForm('schedule/new.html.twig', [
                    'schedule' => $schedule,
                    'form' => $form,
                    'error' => 'Нельзя забронировать'
                ]);
            }
            if($schedule->getDateStart()->diff($schedule->getDateEnd())->days > 0){
                return $this->renderForm('schedule/new.html.twig', [
                    'schedule' => $schedule,
                    'form' => $form,
                    'error' => 'Нельзя забронировать больше чем на 24 часа'
                ]);
            }
            $userId = $form->get('user')->getData();
            if ($userId) {
                $user = $userRepository->find($userId);
                if($user){
                    $schedule->setOwner($user);
                } else {
                    return $this->renderForm('schedule/new.html.twig', [
                        'schedule' => $schedule,
                        'form' => $form,
                        'error' => 'Не заполнено поле пользователя'
                    ]);
                }
            } else {
                return $this->renderForm('schedule/new.html.twig', [
                    'schedule' => $schedule,
                    'form' => $form,
                    'error' => 'Не заполнено поле пользователя'
                ]);
            }
            $pcId = $form->get('pc')->getData();
            if ($pcId) {
                $computer = $computerRepository->find($userId);
                if($computer){
                    $schedule->setComputer($computer);
                } else {
                    return $this->renderForm('schedule/new.html.twig', [
                        'schedule' => $schedule,
                        'form' => $form,
                        'error' => 'Не найден копмьютер'
                    ]);
                }
            } else {
                return $this->renderForm('schedule/new.html.twig', [
                    'schedule' => $schedule,
                    'form' => $form,
                    'error' => 'Не заполнено поле компьютера'
                ]);
            }
            $entityManager->persist($schedule);
            $entityManager->flush();

            return $this->redirectToRoute('app_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('schedule/new.html.twig', [
            'schedule' => $schedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_schedule_show', methods: ['GET'])]
    public function show(Schedule $schedule): Response
    {
        return $this->render('schedule/show.html.twig', [
            'schedule' => $schedule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_schedule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Schedule $schedule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('schedule/edit.html.twig', [
            'schedule' => $schedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_schedule_delete', methods: ['POST'])]
    public function delete(Request $request, Schedule $schedule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schedule->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_schedule_index', [], Response::HTTP_SEE_OTHER);
    }
}
