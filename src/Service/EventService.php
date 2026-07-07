<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Exception\EventValidationException;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventService
{
    private const EDITABLE_FIELDS = ['title', 'description', 'color'];

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return Event[]
     */
    public function getCurrentMonthEventsForUser(User $user): array
    {
        return $this->eventRepository->findCurrentMonthByCreator($user);
    }

    public function getEventForUser(int $id, User $user): Event
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found.');
        }

        if ($event->getCreator()?->getId() !== $user->getId()) {
            throw new AccessDeniedException('You are not allowed to access this event.');
        }

        return $event;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateEvent(int $id, User $user, array $data): Event
    {
        $event = $this->getEventForUser($id, $user);

        $errors = [];
        foreach (self::EDITABLE_FIELDS as $field) {
            if (array_key_exists($field, $data) && !is_string($data[$field])) {
                $errors[] = [
                    'field' => $field,
                    'message' => sprintf('The "%s" field must be a string.', $field),
                ];
            }
        }

        if ($errors) {
            throw new EventValidationException($errors);
        }

        if (array_key_exists('title', $data)) {
            $event->setTitle($data['title']);
        }
        if (array_key_exists('description', $data)) {
            $event->setDescription($data['description']);
        }
        if (array_key_exists('color', $data)) {
            $event->setColor($data['color']);
        }

        $violations = $this->validator->validate($event);
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            throw new EventValidationException($errors);
        }

        $this->entityManager->flush();

        return $event;
    }
}
