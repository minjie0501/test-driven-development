<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $userRepository = $doctrine->getRepository(User::class);
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/recharge-success', name: 'recharge-success')]
    public function recharge(ManagerRegistry $doctrine, Request $request): Response
    {
        $newCreditAmount = 0;
        if (isset($request->request) && $request->request->get('users') != null && $request->request->get('amount')){
            $userId = $request->request->get('users');
            $amount= $request->request->get('amount');

            $entityManager = $doctrine->getManager();
            $user = $entityManager->getRepository(User::class)->find($userId);
            if($user->canRecharge($amount)){
                $currentCredit = $user->getCredit();
                $newCreditAmount=$currentCredit + $amount;
                $user->setCredit($newCreditAmount);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->render('user/success.html.twig', [
            "newCreditAmount" => $newCreditAmount
        ]);
    }
}
