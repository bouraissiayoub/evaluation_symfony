<?php

namespace App\Controller\Api;

use App\Repository\SportEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class EventController extends AbstractController
{
    #[Route('/events', name: 'api_events', methods: ['GET'])]
    public function index(SportEventRepository $repo): JsonResponse
    {
        $events = $repo->findBy(['status' => 'PUBLIE']);

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'sport' => $event->getSport(),
                'participants' => $event->getParticipants(),
                'date' => $event->getEventDate()->format('d/m/Y H:i'),
                'status' => $event->getStatus(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/events/{id}', name: 'api_event_show', methods: ['GET'])]
    public function show(int $id, SportEventRepository $repo): JsonResponse
    {
        $event = $repo->find($id);

        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }

        $issues = [];
        foreach ($event->getIssues() as $issue) {
            $issues[] = [
                'id' => $issue->getId(),
                'label' => $issue->getLabel(),
                'odds' => $issue->getCurrentOdds(),
            ];
        }

        return $this->json([
            'id' => $event->getId(),
            'name' => $event->getName(),
            'sport' => $event->getSport(),
            'participants' => $event->getParticipants(),
            'date' => $event->getEventDate()->format('d/m/Y H:i'),
            'status' => $event->getStatus(),
            'issues' => $issues,
        ]);
    }
}