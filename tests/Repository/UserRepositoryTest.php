<?php

namespace App\Tests\Repository;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @link https://symfony.com/doc/4.4/testing/database.html#functional-testing-of-a-doctrine-repository
     * @link https://github.com/nelmio/alice
     */
    public function testCount()
    {
        self::bootKernel();
        $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/user_repository_fixtures.yaml'
        ]);
        $userRepository = self::$container->get('App\Repository\UserRepository');
        $users = $userRepository->count([]);


        $this->assertEquals(10, $users);
    }
}