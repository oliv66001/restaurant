<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
         $admin = new Users();
            $admin->setEmail('admin@demo.fr');
            $admin->setResetToken('admin');
            $admin->setLastName('Admin');
            $admin->setFirstname('Admin');
            $admin->setPhone('0606060606');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
         $manager->persist($admin);

         $dishesadmin = new Users();
            $dishesadmin->setEmail('dishesadmin@demo.fr');
            $dishesadmin->setResetToken('dishesadmin');
            $dishesadmin->setLastName('dishesAdmin');
            $dishesadmin->setFirstname('dishesAdmin');
            $dishesadmin->setPhone('0606060607');
            $dishesadmin->setRoles(['ROLE_DISHES_ADMIN']);
            $dishesadmin->setPassword($this->passwordEncoder->hashPassword($dishesadmin, 'dishesadmin'));
         $manager->persist($dishesadmin);

         $faker = \Faker\Factory::create('fr_FR');

         for ($usr =1; $usr <= 5; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setResetToken($faker->uuid);
            $user->setLastName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setPhone(str_replace(' ', '', $faker->phoneNumber));
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'user'));
            $manager->persist($user);
         }

        $manager->flush();
    }
}
