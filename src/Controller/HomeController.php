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
        if (isset($request->request)) {
            $request = [
                'date' => $request->request->get('date'),
                'start-time' => $request->request->get('start-time'),
                'end-time' => $request->request->get('end-time'),
            ];
        } else {
            $request = "";
        }

        // $user = new User(false);
        // $user->setEmail("tessst@test.com");
        // $user->setPassword("passssword");
        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($user);
        // $entityManager->flush();

        $entityManager = $doctrine->getManager();
        $room = $entityManager->getRepository(Room::class)->find(8);



        // $booking = new Booking();
        // $booking ->setUserId($user);
        // $booking ->setRoomId($room);
        // $booking->setStartDate(new DateTime('2022-01-17 10:30'));
        // $booking->setEndDate(new DateTime('2022-01-17 12:30'));
        // $room->addBooking($booking);
        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($room);
        // $entityManager->persist($booking);
        // $entityManager->flush();



        $bookings = $room->getBookings()->unwrap();
        $test = [];

        foreach ($bookings as &$value) {
            $test[] = ['start' => $value->getStartDate(), 'end' => $value->getEndDate()];
        }


        $roomRepository = $doctrine->getRepository(Room::class);
        $rooms = $roomRepository->findAll();

        return $this->render('home/index.html.twig', [
            'rooms' => $rooms,
            'request' => $request,
            'bookings' => $bookings,
            'test' => $test
        ]);
    }

    #[Route('/booking', name: 'booking')]
    public function booking(ManagerRegistry $doctrine): Response
    {
        // $room = new User(true);
        // $room ->setPassword("123pass");
        // $room ->setEmail("jordan@doe.com");

        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($room);
        // $entityManager->flush();

        $roomRepository = $doctrine->getRepository(Room::class);
        $rooms = $roomRepository->findAll();

        return $this->render('home/booking.html.twig', [
            'rooms' => $rooms,
        ]);
    }
}
