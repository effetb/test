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
    #[OA\Get(description: 'Get events list', responses: [
        new OA\Response(
            response: '200',
            description: 'Return all events',
            content: new OA\JsonContent(
                ref: new Model(
                    type: Event::class,
                )),
        ),
    ])]
    #[Rest\View()]
    public function list(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Event::class)->findAll();

        $view = View::create($events);

        return $this->handleView($view);
    }
}
