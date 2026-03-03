<?php

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
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setAuthor($this->getUser()->getUserIdentifier());
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Zdarzenie zapisane poprawnie.');
            
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    { 
        $this->denyAccessUnlessGranted('ROLE_USER');

// ADMIN może edytować zawsze
if (!$this->isGranted('ROLE_ADMIN')) {

    // USER tylko swoje
    if ($event->getAuthor() !== $this->getUser()->getUserIdentifier()) {
        throw $this->createAccessDeniedException('Możesz edytować tylko swoje zdarzenia.');
    }

    // USER tylko do 24h
    $limit = (new \DateTimeImmutable())->sub(new \DateInterval('PT24H'));
    if ($event->getCreatedAt() !== null && $event->getCreatedAt() < $limit) {
        throw $this->createAccessDeniedException('Nie można edytować zdarzenia starszego niż 24 godziny.');
    }
}
        

        
    

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

// ADMIN może usuwać zawsze
if (!$this->isGranted('ROLE_ADMIN')) {

    // USER tylko swoje
    if ($event->getAuthor() !== $this->getUser()->getUserIdentifier()) {
        throw $this->createAccessDeniedException('Możesz usuwać tylko swoje zdarzenia.');
    }

    // USER tylko do 24h
    $limit = (new \DateTimeImmutable())->sub(new \DateInterval('PT24H'));
    if ($event->getCreatedAt() !== null && $event->getCreatedAt() < $limit) {
        throw $this->createAccessDeniedException('Nie można usuwać zdarzenia starszego niż 24 godziny.');
    }
}

    $this->denyAccessUnlessGranted('ROLE_USER');

    // ADMIN może wszystko
    if (!$this->isGranted('ROLE_ADMIN')) {

        // USER może usuwać tylko swoje
        if ($event->getAuthor() !== $this->getUser()->getUserIdentifier()) {
            throw $this->createAccessDeniedException('Możesz usuwać tylko swoje zdarzenia.');
        }

        // USER tylko do 24h
        $limit = (new \DateTimeImmutable())->sub(new \DateInterval('PT24H'));
        if ($event->getCreatedAt() !== null && $event->getCreatedAt() < $limit) {
            throw $this->createAccessDeniedException('Nie można usuwać zdarzenia starszego niż 24 godziny.');
        }
    }

    if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($event);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
}
}
