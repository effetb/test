<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\User;
use App\Exception\EventValidationException;
use App\Service\EventService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Rest\Route('/api/events', requirements: ['_locale' => 'fr|en|es_CA|es'], defaults: ['_locale' => 'fr'])]
class EventController extends AbstractFOSRestController
{
    #[Rest\Route('/list', methods: [Request::METHOD_GET])]
    #[OA\Get(description: 'Get the current month events created by the logged-in user', responses: [
        new OA\Response(
            response: '200',
            description: 'Return the current month events created by the logged-in user',
            content: new OA\JsonContent(
                ref: new Model(
                    type: Event::class,
                )),
        ),
    ])]
    #[Rest\View()]
    public function list(EventService $eventService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $view = View::create($eventService->getCurrentMonthEventsForUser($user));

        return $this->handleView($view);
    }

    #[Rest\Route('/{id}', methods: [Request::METHOD_GET], requirements: ['id' => '\d+'])]
    #[OA\Get(description: 'Get the details of a single event', responses: [
        new OA\Response(
            response: '200',
            description: 'Return the event details',
            content: new OA\JsonContent(
                ref: new Model(
                    type: Event::class,
                )),
        ),
        new OA\Response(
            response: '403',
            description: 'The event does not belong to the logged-in user',
        ),
        new OA\Response(
            response: '404',
            description: 'The event does not exist',
        ),
    ])]
    #[Rest\View()]
    public function show(int $id, EventService $eventService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $view = View::create($eventService->getEventForUser($id, $user));

        return $this->handleView($view);
    }

    #[Rest\Route('/{id}', methods: [Request::METHOD_POST], requirements: ['id' => '\d+'])]
    #[OA\Post(
        description: 'Update the editable fields (title, description, color) of an existing event',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'color', type: 'string', enum: Event::COLORS),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Return the updated event',
                content: new OA\JsonContent(
                    ref: new Model(
                        type: Event::class,
                    )),
            ),
            new OA\Response(
                response: '400',
                description: 'The request body is invalid (malformed JSON, wrong field type, or color not in the allowed values)',
            ),
            new OA\Response(
                response: '403',
                description: 'The event does not belong to the logged-in user',
            ),
            new OA\Response(
                response: '404',
                description: 'The event does not exist',
            ),
        ],
    )]
    #[Rest\View()]
    public function update(int $id, Request $request, EventService $eventService): Response
    {
        try {
            $data = $request->toArray();
        } catch (JsonException) {
            throw new BadRequestHttpException('Invalid JSON body.');
        }

        /** @var User $user */
        $user = $this->getUser();

        try {
            $event = $eventService->updateEvent($id, $user, $data);
        } catch (EventValidationException $e) {
            $view = View::create(['errors' => $e->getErrors()], Response::HTTP_BAD_REQUEST);

            return $this->handleView($view);
        }

        $view = View::create($event);

        return $this->handleView($view);
    }
}
