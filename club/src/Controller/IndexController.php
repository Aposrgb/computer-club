<?php

namespace App\Controller;

use App\Entity\Room;
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
}