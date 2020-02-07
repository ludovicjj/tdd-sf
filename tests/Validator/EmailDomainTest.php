<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class EmailDomainTest extends TestCase
{
    public function testRequiredOptions()
    {
        $this->expectException(MissingOptionsException::class);
        new EmailDomain();
    }

    public function testInvalidBlockedOptions()
    {
        $this->expectException(ConstraintDefinitionException::class);
        new EmailDomain(['blocked' => 'azerty']);
    }

    public function testOptionIsSetAsArray()
    {
        $blockedOption = ['a', 'b'];
        $constraintDomainEmail = new EmailDomain(['blocked' => $blockedOption]);
        $this->assertEquals($blockedOption, $constraintDomainEmail->blocked);
    }
}