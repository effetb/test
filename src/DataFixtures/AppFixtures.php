<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // 🔥 OBTENER USER (IMPORTANTE)
        $user = $manager->getRepository(User::class)->find(1);

        if (!$user) {
            throw new \Exception("No user found with ID 1. Create a user first.");
        }

        for ($i = 0; $i < 30; $i++) {

            $event = new Event();

            $event->setTitle($faker->sentence(3));
            $event->setDescription($faker->text(200));

            // 🎨 COLOR VALIDO
            $event->setColor($faker->randomElement([
                'rouge',
                'vert',
                'bleu'
            ]));

            // 👤 FIX CRÍTICO (ESTO ARREGLA TU PROBLEMA)
            $event->setCreator($user);

            // 📅 FECHAS (si tu entidad las tiene)
            if (method_exists($event, 'setStartDate')) {
                $start = $faker->dateTimeBetween('-2 months', 'now');
                $end = $faker->dateTimeBetween($start, '+2 months');

                $event->setStartDate($start);
                $event->setEndDate($end);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}
