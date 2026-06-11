<?php

namespace App\Controller;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/wallet')]
class WalletController extends AbstractController
{
    #[Route('/', name: 'wallet_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('wallet/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/deposit', name: 'wallet_deposit', methods: ['POST'])]
    public function deposit(Request $request, WalletService $walletService): Response
    {
        $amount = (float) $request->request->get('amount');

        if ($amount > 0) {
            $walletService->deposit($this->getUser(), $amount);
        }

        return $this->redirectToRoute('wallet_index');
    }
    #[Route('/set-limits', name: 'wallet_set_limits', methods: ['POST'])]
public function setLimits(Request $request, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $user = $this->getUser();
    $user->setDailyBetLimit($request->request->get('dailyBetLimit') ?: null);
    $user->setWeeklyBetLimit($request->request->get('weeklyBetLimit') ?: null);
    $em->flush();

    $this->addFlash('success', 'Limits updated.');
    return $this->redirectToRoute('wallet_index');
}
}