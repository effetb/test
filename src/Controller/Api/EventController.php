<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
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
    #[OA\Get(
        description: 'Get events list',
        responses: [
            new OA\Response(
                response: '200',
                description: 'Return all events',
                content: new OA\JsonContent(
                    ref: new Model(type: Event::class)
                ),
            ),
        ]
    )]
    #[Rest\View()]
    public function list(ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();

        $start = new \DateTime('first day of this month 00:00:00');
        $end = new \DateTime('last day of this month 23:59:59');


        $events = $doctrine->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->where('e.creator = :user')
            ->andWhere('e.startDate BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();


        $view = View::create($events);

        return $this->handleView($view);

    }
    #[Rest\Route('/{id}', methods: [Request::METHOD_GET])]
    #[OA\Get(description: 'Get event by id')]
    #[Rest\View()]
    public function getEvent(int $id, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $event = $doctrine->getRepository(Event::class)->find($id);

       // 404
        if (!$event) {
            return $this->handleView(
                View::create(['message' => 'Event not found'], 404)
            );
        }

        //403
        if ($event->getCreator() !== $user) {
            return $this->handleView(
                View::create(['message' => 'Forbidden'], 403)
            );
        }

        return $this->handleView(View::create($event));
    }

    #[Rest\Route('/{id}', methods: [Request::METHOD_POST])]
    #[OA\Post(
        description: 'Update event',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'color', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Event updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 400, description: 'Bad request')
        ]
    )]
    #[Rest\View()]
    public function updateEvent(
        int $id,
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $user = $this->getUser();
        $em = $doctrine->getManager();

        $event = $doctrine->getRepository(Event::class)->find($id);

        //  404
        if (!$event) {
            return $this->handleView(
                View::create(['message' => 'Event not found'], 404)
            );
        }

        //  403
        if ($event->getCreator() !== $user) {
            return $this->handleView(
                View::create(['message' => 'Forbidden'], 403)
            );
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->handleView(
                View::create(['message' => 'Invalid JSON'], 400)
            );
        }


        $allowedColors = ['rouge', 'vert', 'bleu'];

        if (isset($data['color']) && !in_array($data['color'], $allowedColors)) {
            return $this->handleView(
                View::create(['message' => 'Invalid color'], 400)
            );
        }

        // ✔ updates
        if (isset($data['title'])) {
            $event->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }

        if (isset($data['color'])) {
            $event->setColor($data['color']);
        }

        $em->flush();

        return $this->handleView(View::create($event));
    }


}

