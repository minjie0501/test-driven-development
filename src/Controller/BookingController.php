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

class BookingController extends AbstractController
{
    // TODO: add details of booking for the success message

    const PRICE_PER_HOUR = 2;

    #[Route('/booking', name: 'booking')]
    public function booking(ManagerRegistry $doctrine, Request $request): Response
    {
        $errorMsg = "";
        if (($request->query->get('error') == true)) {
            $errorMsg = "Sorry! The requested date is not available.";
        }
        $userRepository = $doctrine->getRepository(User::class);
        $users = $userRepository->findAll();
        $roomId = $request->query->get('id');

        return $this->render('booking/index.html.twig', [
            'users' => $users,
            'roomId' => $roomId,
            'errorMsg' => $errorMsg
        ]);
    }

    #[Route('/success', name: 'success')]
    public function success(ManagerRegistry $doctrine, Request $request): Response
    {
        if (isset($request->request) && $request->request->get('date') != null && $request->request->get('start-time') != null && $request->request->get('end-time') != null && $request->request->get('users') != null) {
            $request = [
                'date' => $request->request->get('date'),
                'start-time' => $request->request->get('start-time'),
                'end-time' => $request->request->get('end-time'),
                'userId' => $request->request->get('users'),
                'roomId' => $request->request->get('roomId')
            ];

            $startTime = new DateTime("{$request['date']} {$request['start-time']}");
            $endTime = new DateTime("{$request['date']} {$request['end-time']}");

            // Get user info
            $entityManager = $doctrine->getManager();
            $user = $entityManager->getRepository(User::class)->find($request['userId']);
            $room = $entityManager->getRepository(Room::class)->find($request['roomId']);

            // testPremiumRoom / canBook
            $canBook = $room->canBook($user);

            // testBookTime / canBookTimeFrame
            $canBookTimeFrame = $room->canBookTimeFrame($startTime, $endTime);

            // canAfford
            $diff = $startTime->diff($endTime);
            $mins = $diff->i;
            $hours = $diff->h;
            $hours = $hours + ($diff->days * 24);
            if ($mins > 0) $hours += 1;
            $canAfford = $room->canAfford($user, $hours);

            // isAvailable
            $reservedDates = $room->reservedDates($doctrine);
            $isAvailable = $room->isAvailable($startTime, $endTime, $reservedDates);

            // Create booking
            if ($canBook && $canBookTimeFrame && $canAfford && $isAvailable) {
                $booking = new Booking();
                $booking->setUserId($user);
                $booking->setRoomId($room);
                $booking->setStartDate($startTime);
                $booking->setEndDate($endTime);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($booking);
                $entityManager->flush();

                $currentCredit = $user->getCredit();
                $user->setCredit($currentCredit - (self::PRICE_PER_HOUR * $hours));

                return $this->render('booking/success.html.twig', [
                    'date' => $request['date'],
                    'startTime' => $request['start-time'],
                    'endTime' => $request['end-time'],
                    'roomName' => $room->getName()
                ]);
            }
            return $this->redirectToRoute("booking", array('id' => $request['roomId'], 'error' => "true"));
            $request = "";
            // TODO: do something with this
        }
        // $room = new User(true);
        // $room ->setPassword("123pass");
        // $room ->setEmail("jordan@doe.com");

        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($room);
        // $entityManager->flush();


    }
}
