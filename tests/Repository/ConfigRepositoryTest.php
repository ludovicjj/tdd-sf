<?php

namespace App\Tests\Repository;

use App\Repository\ConfigRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCount()
    {
        self::bootKernel();
        $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/config_repository_fixtures.yaml'
        ]);
        $configRepository = self::$container->get('App\Repository\ConfigRepository');
        $configRepository->count([]);

        $this->assertEquals(2, $configRepository->count([]));
    }

    public function testValue()
    {
        self::bootKernel();

        $config = $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/config_repository_fixtures.yaml'
        ]);
        $this->assertEquals($config['config0']->getValue(), 'baddomaindb.fr');
    }

    public function testGetBlockedDomainAsArray()
    {
        self::bootKernel();
        $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/config_repository_fixtures.yaml'
        ]);
        /** @var ConfigRepository $configRepository */
        $configRepository = self::$container->get('App\Repository\ConfigRepository');
        $records = $configRepository->getBlockedDomainAsArray('blocked');
        var_dump($records);

        $this->assertCount(2, $records);
    }
}