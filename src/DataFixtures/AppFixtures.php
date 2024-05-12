<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //* Actor fixtures

        $actor = new Actor();
        $actor->setName('Chris Evans');
        $manager->persist($actor);

        $actor2 = new Actor();
        $actor2->setName('Jeremy Renner');
        $manager->persist($actor2);

        $actor3 = new Actor();
        $actor3->setName('Jennie Kwan');
        $manager->persist($actor3);

        $actor4 = new Actor();
        $actor4->setName('Mako');
        $manager->persist($actor4);
        $manager->flush();

        $this->addReference('actor_1', $actor);
        $this->addReference('actor_2', $actor2);
        $this->addReference('actor_3', $actor3);
        $this->addReference('actor_4', $actor4);

        //* Movie fixtures 

        $movie = new Movie();
        $movie->setTitle('Avengers');
        $movie->setDescription('This is description for Avengers');
        $movie->setReleaseYear(2012);
        $movie->setImagePath('https://images.unsplash.com/photo-1561149877-84d268ba65b8?q=80&w=1935&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        // add data to pivot table movie_actor
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('Avatar The Last Air Bender');
        $movie2->setDescription('This is description for Avatar The Last Air Bender');
        $movie2->setReleaseYear(2024);
        $movie2->setImagePath('https://images.unsplash.com/photo-1494931216633-3f436bf43829?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        // add data to pivot table movie_actor
        $movie->addActor($this->getReference('actor_3'));
        $movie->addActor($this->getReference('actor_4'));

        $manager->persist($movie2);

        $manager->flush();
    }
}
