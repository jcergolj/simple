<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ResetUserPassword;
use Illuminate\Console\Command;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Console\Commands\ResetUserPassword::class)]
class ResetUserPasswordTest extends TestCase
{
    #[Test]
    public function command_signature_is_correct(): void
    {
        $command = new ResetUserPassword;

        $this->assertSame('user:reset-password', $command->getName());
    }

    #[Test]
    public function command_description_is_set(): void
    {
        $command = new ResetUserPassword;

        $this->assertSame('Reset a user\'s password by email address', $command->getDescription());
    }

    #[Test]
    public function command_extends_base_command(): void
    {
        $command = new ResetUserPassword;

        $this->assertInstanceOf(Command::class, $command);
    }

    #[Test]
    public function command_has_correct_properties(): void
    {
        $reflection = new \ReflectionClass(ResetUserPassword::class);

        $this->assertTrue($reflection->hasProperty('signature'));
        $this->assertTrue($reflection->hasProperty('description'));
        $this->assertTrue($reflection->hasMethod('handle'));
    }

    #[Test]
    public function handle_method_returns_int(): void
    {
        $reflection = new \ReflectionMethod(ResetUserPassword::class, 'handle');

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionType::class, $returnType);
        $this->assertSame('int', $returnType->getName());
    }

    #[Test]
    public function command_has_email_argument(): void
    {
        $command = new ResetUserPassword;

        $this->assertTrue($command->getDefinition()->hasArgument('email'));
    }

    #[Test]
    public function command_has_password_option(): void
    {
        $command = new ResetUserPassword;

        $this->assertTrue($command->getDefinition()->hasOption('password'));
    }

    #[Test]
    public function command_constants_are_accessible(): void
    {
        $this->assertSame(0, ResetUserPassword::SUCCESS);
        $this->assertSame(1, ResetUserPassword::FAILURE);
    }
}
