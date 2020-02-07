<?php

namespace App\Tests\Validator;


use App\Repository\ConfigRepository;
use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @param bool $expectedViolation
     * @param array $dbBlockedDomain
     * @return EmailDomainValidator
     */
    private function getValidator($expectedViolation = false, $dbBlockedDomain = [])
    {
        // TODO mock configRepository
        $configRepository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects($this->any())
            ->method('getBlockedDomainAsArray')
            ->with($this->equalTo('blocked'))
            ->willReturn($dbBlockedDomain);

        $validator = new EmailDomainValidator($configRepository);
        $context = $this->getContext($expectedViolation);

        $validator->initialize($context);
        return $validator;
    }

    /**
     * @param bool $expectedViolation
     * @return ExecutionContextInterface
     */
    private function getContext(bool $expectedViolation): ExecutionContextInterface
    {
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        if ($expectedViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->expects($this->any())->method('setParameter')->willReturn($violation);
            $violation->expects($this->any())->method('addViolation');

            $context
                ->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation);

        } else {
            $context
                ->expects($this->never())
                ->method('buildViolation');
        }

        return $context;
    }

    public function testBadDomainFromDatabaseWithFixtures()
    {
        $constraint = new EmailDomain(['blocked' => []]);
        self::bootKernel();
        $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/config_repository_fixtures.yaml'
        ]);

        $validator = self::$container->get('App\Validator\EmailDomainValidator');
        $validator->initialize($this->getContext(true));
        $validator->validate('demo@devildomaindb.com', $constraint);
    }

    public function testBadDomainFromDatabaseWithoutFixtures()
    {
        $constraint = new EmailDomain(['blocked' => []]);
        $this->getValidator(true, ['yolo.fr'])
            ->validate('demo@yolo.fr', $constraint);
    }

    public function testBadDomainWithParamsFromServiceYaml()
    {
        $constraint = new EmailDomain(['blocked' => []]);
        self::bootKernel();
        $validator = self::$container->get('App\Validator\EmailDomainValidator');

        $validator->initialize($this->getContext(true));
        $validator->validate('demo@globalblocked.fr', $constraint);
    }

    public function testBadDomainFromConstraint()
    {
        $constraint = new EmailDomain(['blocked' => ['leviathan.fr', 'hello.com']]);
        $this->getValidator(true)->validate('demo@leviathan.fr', $constraint);
    }

    public function testGoodDomain()
    {
        $constraint = new EmailDomain(['blocked' => ['leviathan.fr', 'hello.com']]);
        $this->getValidator(false)->validate('demo@gooddomain.fr', $constraint);
    }
}