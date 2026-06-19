<?php

namespace App\Tests\Controller\Api;

use App\Entity\User;
use App\Enum\ColorEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;
    private string $token;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->token = $this->authenticate();
    }

    private function authenticate(): string
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => 'admin@effetb.com', 'password' => 'admin'])
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        return $data['token'] ?? '';
    }

    public function testLoginReturnsToken(): void
    {
        $this->assertNotEmpty($this->token);
    }

    public function testListRequiresAuthentication(): void
    {
        $this->client->request('GET', '/api/events/list');
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListReturnsEvents(): void
    {
        $this->client->request(
            'GET',
            '/api/events/list',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);

        if (!empty($data)) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('title', $data[0]);
            $this->assertArrayHasKey('color', $data[0]);
        }
    }

    public function testShowReturnsEvent(): void
    {
        $eventId = $this->getFirstEventId();

        $this->client->request(
            'GET',
            "/api/events/{$eventId}",
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('color', $data);
    }

    public function testShowReturns404ForNonExistentEvent(): void
    {
        $this->client->request(
            'GET',
            '/api/events/99999',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateEvent(): void
    {
        $eventId = $this->getFirstEventId();

        $this->client->request(
            'POST',
            "/api/events/{$eventId}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode(['title' => 'Updated title', 'color' => 'bleu'])
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('Updated title', $data['title']);
        $this->assertSame('bleu', $data['color']);
    }

    public function testUpdateReturns400ForInvalidColor(): void
    {
        $eventId = $this->getFirstEventId();

        $this->client->request(
            'POST',
            "/api/events/{$eventId}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode(['color' => 'invalid'])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function getFirstEventId(): int
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@effetb.com']);

        $event = $this->entityManager->getRepository(\App\Entity\Event::class)
            ->findForCurrentMonthByCreator($user);

        if (empty($event)) {
            $this->markTestSkipped('No events found for current month');
        }

        return $event[0]->getId();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
