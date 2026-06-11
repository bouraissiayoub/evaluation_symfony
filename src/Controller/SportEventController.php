<?php

namespace App\Controller;

use App\Entity\SportEvent;
use App\Repository\SportEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/manager/event')]
class SportEventController extends AbstractController
{
    #[Route('/', name: 'event_index')]
    public function index(SportEventRepository $repo): Response
    {
        return $this->render('sport_event/index.html.twig', [
            'events' => $repo->findAll(),
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
}