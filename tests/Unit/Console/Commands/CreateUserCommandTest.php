<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\CreateUserCommand;
use Illuminate\Console\Command;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Console\Commands\CreateUserCommand::class)]
class CreateUserCommandTest extends TestCase
{
    #[Test]
    public function command_signature_is_correct(): void
    {
        $command = new CreateUserCommand;

        $this->assertSame('app:create-user', $command->getName());
    }

    #[Test]
    public function command_description_is_set(): void
    {
        $command = new CreateUserCommand;

        $this->assertSame('Create a new user for the time tracking application', $command->getDescription());
    }

    #[Test]
    public function command_extends_base_command(): void
    {
        $command = new CreateUserCommand;

        $this->assertInstanceOf(Command::class, $command);
    }

    #[Test]
    public function command_has_correct_properties(): void
    {
        $reflection = new \ReflectionClass(CreateUserCommand::class);

        $this->assertTrue($reflection->hasProperty('signature'));
        $this->assertTrue($reflection->hasProperty('description'));
        $this->assertTrue($reflection->hasMethod('handle'));
    }

    #[Test]
    public function handle_method_returns_int(): void
    {
        $reflection = new \ReflectionMethod(CreateUserCommand::class, 'handle');

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionType::class, $returnType);
        $this->assertSame('int', $returnType->getName());
    }

    #[Test]
    public function command_constants_are_accessible(): void
    {
        $this->assertSame(0, CreateUserCommand::SUCCESS);
        $this->assertSame(1, CreateUserCommand::FAILURE);
    }
}
