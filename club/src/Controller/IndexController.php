<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    #[Route('/room/{room}', name: 'get_room', methods: ['GET'])]
    public function getRoom(string $room): Response
    {
        if(!is_numeric($room)){
            return $this->redirectToRoute('index');
        }
        return $this->render('room/room_index.html.twig');
    }

    #[Route('/computer/{computer}', name: 'get_computer_page', methods: ['GET'])]
    public function getComputer(string $computer): Response
    {
        if(!is_numeric($computer)){
            return $this->redirectToRoute('index');
        }
        return $this->render('computer/computer.html.twig');
    }

    #[Route('/purchases', name: 'get_purchases', methods: ['GET'])]
    public function getPurchases(): Response
    {
        return $this->render('purchase/purchase_index.html.twig');
    }
}