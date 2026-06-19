<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\User;
use App\Enum\ColorEnum;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Rest\Route('/{id}', methods: [Request::METHOD_POST])]
    #[OA\Post(
        description: 'Update an existing event',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'color', type: 'string', enum: ['rouge', 'vert', 'bleu']),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Return updated event',
                content: new OA\JsonContent(ref: new Model(type: Event::class))
            ),
            new OA\Response(response: '400', description: 'Validation error'),
            new OA\Response(response: '403', description: 'Forbidden'),
            new OA\Response(response: '404', description: 'Not found'),
        ]
    )]
    #[Rest\View()]
    public function update(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request, int $id): Response
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

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->handleView(View::create(['message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST));
        }

        if (array_key_exists('title', $payload)) {
            $event->setTitle((string) $payload['title']);
        }

        if (array_key_exists('description', $payload)) {
            $event->setDescription((string) $payload['description']);
        }

        if (array_key_exists('color', $payload)) {
            $event->setColor(ColorEnum::from((string) $payload['color']));
        }

        $violations = $validator->validate($event);

        if ($violations->count() > 0) {
            return $this->handleView(View::create([
                'message' => 'Validation failed',
                'errors' => $this->formatViolations($violations),
            ], Response::HTTP_BAD_REQUEST));
        }

        $doctrine->getManager()->flush();

        return $this->handleView(View::create($event));
    }

    private function formatViolations(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
