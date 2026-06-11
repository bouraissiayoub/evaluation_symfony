<?php

namespace App\DataFixtures;

use App\Entity\Bet;
use App\Entity\Issue;
use App\Entity\SportEvent;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
                // --- USERS ---
        $admin = new User();
        $admin->setEmail('admin@bet.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Boss');
        $admin->setBirthDate(new \DateTime('1990-01-01'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setBalance(0);
        $admin->setIsActive(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);


        $manager2 = new User();
        $manager2->setEmail('manager@bet.com');
        $manager2->setFirstName('Manager');
        $manager2->setLastName('Sport');
        $manager2->setBirthDate(new \DateTime('1992-05-15'));
        $manager2->setRoles(['ROLE_MANAGER']);
        $manager2->setBalance(0);
        $manager2->setIsActive(true);
        $manager2->setPassword($this->hasher->hashPassword($manager2, 'password'));
        $manager->persist($manager2);


        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setBirthDate($faker->dateTimeBetween('-50 years', '-18 years'));
            $user->setRoles(['ROLE_USER']);
            $user->setBalance(500.00);
            $user->setIsActive(true);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

// --- EVENTS ---
        $eventsData = [
            ['name' => 'PSG vs Marseille', 'sport' => 'Football', 'participants' => 'PSG, Marseille'],
            ['name' => 'Nadal vs Djokovic', 'sport' => 'Tennis', 'participants' => 'Nadal, Djokovic'],
            ['name' => 'Lakers vs Bulls', 'sport' => 'Basketball', 'participants' => 'Lakers, Bulls'],
        ];

        $events = [];
        foreach ($eventsData as $data) {
            $event = new SportEvent();
            $event->setName($data['name']);
            $event->setSport($data['sport']);
            $event->setParticipants($data['participants']);
            $event->setEventDate($faker->dateTimeBetween('+1 day', '+30 days'));
            $event->setStatus('PUBLIE');
            $manager->persist($event);
            $events[] = $event;



            // --- ISSUES ---
            $issuesData = ['Team A wins', 'Draw', 'Team B wins'];
            $issues = [];
            foreach ($issuesData as $label) {
                $issue = new Issue();
                $issue->setLabel($label);
                $issue->setCurrentOdds(1.50);
                $issue->setTotalAmountBet(0.0);
                $issue->setSportEvent($event);
                $manager->persist($issue);
                $issues[] = $issue;
            }
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
