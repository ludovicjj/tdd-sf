<?php
/**
 * Created by PhpStorm.
 * User: Ludovic
 * Date: 04/02/2020
 * Time: 03:14
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $user = (new User())
                ->setEmail("user$i@domain.fr")
                ->setPassword('0000');
            $manager->persist($user);
        }
        $manager->flush();
    }
}
