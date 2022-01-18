<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Booking;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

//TODO: Hash passwords 
class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        // $user = new User(false);
        // $user->setEmail("tessst@test.com");
        // $user->setPassword("passssword");
        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($user);
        // $entityManager->flush();

        $roomRepository = $doctrine->getRepository(Room::class);
        $rooms = $roomRepository->findAll();

        return $this->render('home/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }


}
