<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SportEventRepository;
use App\Repository\IssueRepository;
use App\Service\BettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bet')]
class BetController extends AbstractController
{
    #[Route('/', name: 'bet_index')]
    public function index(SportEventRepository $repo): Response
    {
        return $this->render('bet/index.html.twig', [
            'events' => $repo->findBy(['status' => 'PUBLIE']),
        ]);
    }

    #[Route('/place', name: 'bet_place', methods: ['POST'])]
    public function place(Request $request, IssueRepository $issueRepo, BettingService $bettingService): Response
    {
        $issue = $issueRepo->find($request->request->get('issueId'));
        $amount = (float) $request->request->get('amount');

        $result = $bettingService->placeBet($this->getUser(), $issue, $amount);

        if ($result !== 'success') {
            $this->addFlash('error', $result);
        }

        return $this->redirectToRoute('bet_index');
    }
    #[Route('/', name: 'app_home')]
public function home(): Response
{
    return $this->redirectToRoute('bet_index');
}
#[Route('/history', name: 'bet_history')]
public function history(): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');
    
    return $this->render('bet/history.html.twig', [
        'bets' => $this->getUser()->getBets(),
    ]);
}
#[Route('/self-exclude', name: 'self_exclude', methods: ['POST'])]
public function selfExclude(Request $request, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $until = new \DateTime($request->request->get('until'));
    $this->getUser()->setSelfExclusionEnd($until);
    $em->flush();

$this->addFlash('success', sprintf('You have been self-excluded until %s', $until->format('d/m/Y')));

    return $this->redirectToRoute('bet_index');
}
}