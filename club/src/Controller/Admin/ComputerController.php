<?php

namespace App\Controller\Admin;

use App\Entity\Computer;
use App\Form\ComputerType;
use App\Repository\ComputerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/computer')]
class ComputerController extends AbstractController
{
    #[Route('/', name: 'app_computer_index', methods: ['GET'])]
    public function index(ComputerRepository $computerRepository): Response
    {
        return $this->render('computer/index.html.twig', [
            'computers' => $computerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_computer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $computer = new Computer();
        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($computer);
            $entityManager->flush();

            return $this->redirectToRoute('app_computer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('computer/new.html.twig', [
            'computer' => $computer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_computer_show', methods: ['GET'])]
    public function show(Computer $computer): Response
    {
        return $this->render('computer/show.html.twig', [
            'computer' => $computer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_computer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Computer $computer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ComputerType::class, $computer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_computer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('computer/edit.html.twig', [
            'computer' => $computer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_computer_delete', methods: ['POST'])]
    public function delete(Request $request, Computer $computer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$computer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($computer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_computer_index', [], Response::HTTP_SEE_OTHER);
    }
}
