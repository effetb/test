<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use App\Enum\ColorEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create('fr_FR');
        $populator = new Populator($generator, $manager);
        $populator->addEntity(User::class, 1, [ 'email' => 'admin@effetb.com', 'password' => 'admin']);
        $populator->addEntity(Event::class, 10, [
            'title' => function () use ($generator) {
                return $generator->realText(100);
            },
            'description' => function () use ($generator) {
                return $generator->realText(5000);
            },
            'startDate' => function () use ($generator) {
                return $generator->dateTimeBetween('-2 months', 'now');
            },
            'endDate' => function ($insertedIds, $obj) use ($generator) {
                // $obj est l'entité Event en cours de génération
                $startDate = $obj->getStartDate();

                // Générer une endDate entre startDate et +3 mois après startDate
                return $generator->dateTimeBetween(
                    $startDate,
                    (clone $startDate)->modify('+2 months')
                );
            },
        ]);
        $populator->execute();
        $populator->addEntity(User::class, 3, [ 'password' => 'admin']);
        $populator->addEntity(Event::class, 50, [
            'title' => function () use ($generator) {
                return $generator->realText(100);
            },
            'description' => function () use ($generator) {
                return $generator->realText(5000);
            },
            'startDate' => function () use ($generator) {
                return $generator->dateTimeBetween('-2 months', 'now');
            },
            'endDate' => function ($insertedIds, $obj) use ($generator) {
                // $obj est l'entité Event en cours de génération
                $startDate = $obj->getStartDate();

                // Générer une endDate entre startDate et +3 mois après startDate
                return $generator->dateTimeBetween(
                    $startDate,
                    (clone $startDate)->modify('+2 months')
                );
            },
            'color' => fn() => $generator->randomElement(ColorEnum::cases()),
        ]);
        $populator->execute();
    }
}
