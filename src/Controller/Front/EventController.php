<?php

namespace App\Controller\Front;

use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'events')]
    public function events(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $events = $em->getRepository(Event::class)->findAll();

        return $this->render('front/event/index.html.twig', ['events' => $events]);
    }
}
