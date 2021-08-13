<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Recibo;
use App\Entity\User;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user= $manager->getRepository(User::class)->findAll();
        //
        
    }
}
