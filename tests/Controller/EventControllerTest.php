<?php

namespace App\Tests\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EventControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $eventRepository;
    private string $path = '/event/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->eventRepository = $this->manager->getRepository(Event::class);

        foreach ($this->eventRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Event index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'event[occurredAt]' => 'Testing',
            'event[description]' => 'Testing',
            'event[type]' => 'Testing',
            'event[place]' => 'Testing',
            'event[createdAt]' => 'Testing',
            'event[updatedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/event');

        self::assertSame(1, $this->eventRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Event();
        $fixture->setOccurredAt('My Title');
        $fixture->setDescription('My Title');
        $fixture->setType('My Title');
        $fixture->setPlace('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Event');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Event();
        $fixture->setOccurredAt('Value');
        $fixture->setDescription('Value');
        $fixture->setType('Value');
        $fixture->setPlace('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'event[occurredAt]' => 'Something New',
            'event[description]' => 'Something New',
            'event[type]' => 'Something New',
            'event[place]' => 'Something New',
            'event[createdAt]' => 'Something New',
            'event[updatedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/event');

        $fixture = $this->eventRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getOccurredAt());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getPlace());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Event();
        $fixture->setOccurredAt('Value');
        $fixture->setDescription('Value');
        $fixture->setType('Value');
        $fixture->setPlace('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/event');
        self::assertSame(0, $this->eventRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
