<?php

namespace App\Controller;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}