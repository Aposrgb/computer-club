<?php

namespace App\Controller\Admin;

use App\Entity\EntityType;
use App\Form\EntityTypeType;
use App\Repository\EntityTypeRepository;
use App\Service\EntityTypeService;
use App\Service\FileUploadService;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entity/type')]
class EntityTypeController extends AbstractController
{
    #[Route('/', name: 'app_entity_type_index', methods: ['GET'])]
    public function index(EntityTypeRepository $entityTypeRepository): Response
    {
        return $this->render('entity_type/index.html.twig', [
            'entity_types' => $entityTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_entity_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EntityTypeService $service, ValidatorService $validatorService): Response
    {
        $entityType = new EntityType();
        $form = $this->createForm(EntityTypeType::class, $entityType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entityType);
            $entityManager->flush();

            return $this->redirectToRoute('app_entity_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entity_type/new.html.twig', [
            'entity_type' => $entityType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entity_type_show', methods: ['GET'])]
    public function show(EntityType $entityType): Response
    {
        return $this->render('entity_type/show.html.twig', [
            'entity_type' => $entityType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entity_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityType $entityType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntityTypeType::class, $entityType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_entity_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entity_type/edit.html.twig', [
            'entity_type' => $entityType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entity_type_delete', methods: ['POST'])]
    public function delete(Request $request, EntityType $entityType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entityType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entityType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entity_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
