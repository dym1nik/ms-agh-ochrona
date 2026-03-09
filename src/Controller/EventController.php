<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'app_event_index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('event/index.html.twig', [
            'events' => $this->eventRepository->findAll(),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'currentUserIdentifier' => $user?->getUserIdentifier(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setAuthor($this->getUser()?->getUserIdentifier());

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $this->addFlash('success', 'Zdarzenie zapisane poprawnie.');

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show')]
    public function show(Event $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $event);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->touch();
            $this->entityManager->flush();

            $this->addFlash('success', 'Zdarzenie zaktualizowane.');

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $event);

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();

            $this->addFlash('success', 'Zdarzenie usunięte.');
        }

        return $this->redirectToRoute('app_event_index');
    }
}