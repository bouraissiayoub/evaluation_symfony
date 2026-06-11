<?php

namespace App\Controller;

use App\Entity\SportEvent;
use App\Repository\SportEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\IssueRepository;
use App\Service\BettingService;
use App\Entity\Issue;

#[Route('/manager/event')]
class SportEventController extends AbstractController
{
    #[Route('/', name: 'event_index')]
  #[Route('/', name: 'event_index')]
public function index(SportEventRepository $repo, Request $request): Response
{
    $page = $request->query->getInt('page', 1);
    $events = $repo->findPaginated($page);

    return $this->render('sport_event/index.html.twig', [
        'events' => $events,
        'page' => $page,
    ]);
}

    #[Route('/new', name: 'event_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    if ($request->isMethod('POST')) {
        $event = new SportEvent();
        $event->setName($request->request->get('name'));
        $event->setSport($request->request->get('sport'));
        $event->setParticipants($request->request->get('participants'));
        $event->setEventDate(new \DateTime($request->request->get('eventDate')));
        $event->setStatus('BROUILLON');

        $em->persist($event);

        foreach ($request->request->all('issues') as $label) {
            if ($label !== '') {
                $issue = new Issue();
                $issue->setLabel($label);
                $issue->setCurrentOdds(1.50);
                $issue->setTotalAmountBet(0.0);
                $issue->setSportEvent($event);
                $em->persist($issue);
            }
        }

        $em->flush();
        return $this->redirectToRoute('event_index');
    }

    return $this->render('sport_event/new.html.twig');
}

    #[Route('/{id}/publish', name: 'event_publish')]
    public function publish(SportEvent $event, EntityManagerInterface $em): Response
    {
        $event->setStatus('PUBLIE');
        $em->flush();
        return $this->redirectToRoute('event_index');
    }

    #[Route('/{id}/delete', name: 'event_delete', methods: ['POST'])]
    public function delete(SportEvent $event, EntityManagerInterface $em): Response
    {
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('event_index');
    }

       #[Route('/{id}/close', name: 'event_close')]
    public function close(SportEvent $event, EntityManagerInterface $em): Response
    {
        $event->setStatus('FERME');
        $em->flush();
        return $this->redirectToRoute('event_index');
    }

    #[Route('/{id}/resolve/{issueId}', name: 'event_resolve')]
    public function resolve(int $id, int $issueId, SportEventRepository $repo, IssueRepository $issueRepo, BettingService $bettingService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $event = $repo->find($id);
        $winningIssue = $issueRepo->find($issueId);

        $bettingService->resolveEvent($event, $winningIssue);

        return $this->redirectToRoute('event_index');
    }
    
}