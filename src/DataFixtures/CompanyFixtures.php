<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public const COMPANY = 'companie';
    public const BILEMO = 'bileMo';

    public function load(ObjectManager $manager)
    {
        $bilemo = new Company();
        $bilemo->setName('bileMo');
        $bilemo->setAddress('Rue de Paris - 75000 Paris');
        $bilemo->setSiret('111 111 111 111');
        $this->setReference(self::BILEMO, $bilemo);
        $manager->persist($bilemo);

        for ($i = 0; $i < 5; $i++) {
            $company = new Company();
            $company->setName('Entreprise de téléphone ' . $i);
            $company->setAddress(($i . ' rue du monde - ' . '7500' . $i . ' Paris'));
            $company->setSiret('435 085 625 00' . $i);
            $this->setReference(self::COMPANY . $i, $company);
            $manager->persist($company);
        }
        $manager->flush();
    }
}
