<?php

namespace App\Tests\Entity;

use App\Entity\InvitationCode;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class InvitationCodeTest extends KernelTestCase
{
    use FixturesTrait;

    private function makeInvitation(): InvitationCode
    {
        return (new InvitationCode())
            ->setCode('12345')
            ->setDescription('ma super description')
            ->setExpireAt(new \DateTime());
    }

    private function assertHasError(InvitationCode $code, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($code);
        $message = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $message[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $message));
    }

    public function testValidEntity()
    {
        $code = $this->makeInvitation();
        $this->assertHasError($code, 0);
    }

    public function testInvalidRegexCodeEntity()
    {
        $this->assertHasError($this->makeInvitation()->setCode('1a345'), 1);
        $this->assertHasError($this->makeInvitation()->setCode('1345'), 1);
    }

    public function testInvalidBlankCodeEntity()
    {
        $this->assertHasError($this->makeInvitation()->setCode(''), 1);
    }

    public function testInvalidBlankDescriptionEntity()
    {
        $this->assertHasError($this->makeInvitation()->setDescription(''), 1);
    }

    public function testInvalidUniqueCodeEntity()
    {
        $this->loadFixtureFiles([
           dirname(__DIR__) . '/fixtures/invitation_code_fixtures.yaml'
        ]);
        $this->assertHasError($this->makeInvitation()->setCode('54321'), 1);
    }
}