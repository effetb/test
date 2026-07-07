<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Controller\Api\EventController;
use App\Entity\Event;
use App\Entity\User;
use App\Exception\EventValidationException;
use App\Service\EventService;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EventControllerTest extends TestCase
{
    private function createController(User $user): EventController
    {
        $controller = new EventController();

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createStub(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($tokenStorage);
        $controller->setContainer($container);

        $viewHandler = $this->createStub(ViewHandlerInterface::class);
        $viewHandler->method('handle')->willReturnCallback(
            static fn (View $view): Response => new Response(
                (string) json_encode($view->getData()),
                $view->getStatusCode() ?? Response::HTTP_OK
            )
        );
        $controller->setViewHandler($viewHandler);

        return $controller;
    }

    public function testListReturnsCurrentMonthEventsForTheLoggedInUser(): void
    {
        $user = new User();
        $event = new Event();

        $eventService = $this->createMock(EventService::class);
        $eventService->expects($this->once())
            ->method('getCurrentMonthEventsForUser')
            ->with($user)
            ->willReturn([$event]);

        $response = $this->createController($user)->list($eventService);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testShowReturnsTheEventWhenItBelongsToTheLoggedInUser(): void
    {
        $user = new User();
        $event = new Event();

        $eventService = $this->createMock(EventService::class);
        $eventService->expects($this->once())
            ->method('getEventForUser')
            ->with(61, $user)
            ->willReturn($event);

        $response = $this->createController($user)->show(61, $eventService);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testShowPropagatesNotFoundExceptionFromTheService(): void
    {
        $user = new User();

        $eventService = $this->createStub(EventService::class);
        $eventService->method('getEventForUser')
            ->willThrowException(new NotFoundHttpException('Event not found.'));

        $this->expectException(NotFoundHttpException::class);

        $this->createController($user)->show(999, $eventService);
    }

    public function testShowPropagatesAccessDeniedExceptionFromTheService(): void
    {
        $user = new User();

        $eventService = $this->createStub(EventService::class);
        $eventService->method('getEventForUser')
            ->willThrowException(new AccessDeniedException('You are not allowed to access this event.'));

        $this->expectException(AccessDeniedException::class);

        $this->createController($user)->show(77, $eventService);
    }

    public function testUpdateReturnsTheUpdatedEvent(): void
    {
        $user = new User();
        $event = new Event();
        $payload = ['title' => 'New title', 'color' => 'rouge'];

        $eventService = $this->createMock(EventService::class);
        $eventService->expects($this->once())
            ->method('updateEvent')
            ->with(61, $user, $payload)
            ->willReturn($event);

        $request = new Request([], [], [], [], [], [], (string) json_encode($payload));

        $response = $this->createController($user)->update(61, $request, $eventService);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdateReturns400WhenTheServiceReportsValidationErrors(): void
    {
        $user = new User();
        $errors = [['field' => 'color', 'message' => "Cette valeur doit être l'un des choix proposés."]];

        $eventService = $this->createStub(EventService::class);
        $eventService->method('updateEvent')
            ->willThrowException(new EventValidationException($errors));

        $request = new Request([], [], [], [], [], [], (string) json_encode(['color' => 'jaune']));

        $response = $this->createController($user)->update(61, $request, $eventService);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertSame(['errors' => $errors], json_decode((string) $response->getContent(), true));
    }

    public function testUpdateThrowsBadRequestOnMalformedJsonBody(): void
    {
        $user = new User();

        $eventService = $this->createMock(EventService::class);
        $eventService->expects($this->never())->method('updateEvent');

        $request = new Request([], [], [], [], [], [], '{not valid json');

        $this->expectException(BadRequestHttpException::class);

        $this->createController($user)->update(61, $request, $eventService);
    }

    public function testUpdatePropagatesNotFoundExceptionFromTheService(): void
    {
        $user = new User();

        $eventService = $this->createStub(EventService::class);
        $eventService->method('updateEvent')
            ->willThrowException(new NotFoundHttpException('Event not found.'));

        $request = new Request([], [], [], [], [], [], (string) json_encode(['title' => 'x']));

        $this->expectException(NotFoundHttpException::class);

        $this->createController($user)->update(999, $request, $eventService);
    }

    public function testUpdatePropagatesAccessDeniedExceptionFromTheService(): void
    {
        $user = new User();

        $eventService = $this->createStub(EventService::class);
        $eventService->method('updateEvent')
            ->willThrowException(new AccessDeniedException('You are not allowed to update this event.'));

        $request = new Request([], [], [], [], [], [], (string) json_encode(['title' => 'x']));

        $this->expectException(AccessDeniedException::class);

        $this->createController($user)->update(77, $request, $eventService);
    }
}
