<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $superAdmin = new User();
        $superAdmin->setEmail('superadmin@gmail.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $password = $this->hasher->hashPassword($superAdmin, 'coucou');
        $superAdmin->setPassword($password);
        $superAdmin->setCompany($this->getReference(CompanyFixtures::BILEMO));
        $manager->persist($superAdmin);

        for ($i = 0; $i < 5; $i++) {
            $admin = new User();
            $admin->setEmail('user' . $i . '@gmail.com');
            $admin->setRoles(['ROLE_ADMIN']);
            $password = $this->hasher->hashPassword($admin, 'coucou');
            $admin->setPassword($password);
            $admin->setCompany($this->getReference(CompanyFixtures::COMPANY . $i));
            $manager->persist($admin);
        }

        for ($i = 6; $i < 31; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setRoles(['ROLE_USER']);
            $password = $this->hasher->hashPassword($user, 'coucou');
            $user->setPassword($password);
            $user->setCompany($this->getReference(CompanyFixtures::COMPANY . random_int(0, 4)));
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}
