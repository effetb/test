<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/api/events', requirements: ['_locale' => 'fr|en|es_CA|es'], defaults: ['_locale' => 'fr'])]
class EventController extends AbstractFOSRestController
{
    #[Rest\Route('/list', methods: [Request::METHOD_GET])]
    #[OA\Get(description: 'Get events list for the current month and authenticated user', responses: [
        new OA\Response(
            response: '200',
            description: 'Return filtered events',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: new Model(type: Event::class)),
            ),
        ),
    ])]
    #[Rest\View()]
    public function list(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->handleView(View::create(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED));
        }

        $events = $doctrine->getRepository(Event::class)
            ->findForCurrentMonthByCreator($user);

        $view = View::create($events);

        return $this->handleView($view);
    }

    #[Rest\Route('/{id}', methods: [Request::METHOD_GET])]
    #[OA\Get(
        description: 'Get a single event details',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Return event details',
                content: new OA\JsonContent(ref: new Model(type: Event::class))
            ),
            new OA\Response(response: '403', description: 'Forbidden'),
            new OA\Response(response: '404', description: 'Not found'),
        ]
    )]
    #[Rest\View()]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->handleView(View::create(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED));
        }

        $event = $doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            return $this->handleView(View::create(['message' => 'Event not found'], Response::HTTP_NOT_FOUND));
        }

        if ($event->getCreator()?->getId() !== $user->getId()) {
            return $this->handleView(View::create(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN));
        }

        return $this->handleView(View::create($event));
    }
}
