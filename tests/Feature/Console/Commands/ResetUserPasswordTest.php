<?php

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resets_password_with_email_argument(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $this->artisan('user:reset-password', [
            'email' => 'user@example.com',
            '--password' => 'newpassword123',
        ])
            ->expectsOutputToContain("Password successfully reset for user: {$user->name} (user@example.com)")
            ->assertExitCode(0);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    #[Test]
    public function prompts_for_password_when_not_provided(): void
    {
        $user = User::factory()->create([
            'email' => 'prompt@example.com',
            'name' => 'Prompt User',
        ]);

        $this->artisan('user:reset-password', ['email' => 'prompt@example.com'])
            ->expectsQuestion('Enter new password for Prompt User', 'interactive123')
            ->expectsQuestion('Confirm new password', 'interactive123')
            ->expectsOutputToContain('Password successfully reset for user: Prompt User (prompt@example.com)')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertTrue(Hash::check('interactive123', $user->password));
    }

    #[Test]
    public function fails_when_user_not_found(): void
    {
        $this->artisan('user:reset-password', [
            'email' => 'nonexistent@example.com',
            '--password' => 'somepassword',
        ])
            ->expectsOutput("User with email 'nonexistent@example.com' not found.")
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_when_passwords_do_not_match(): void
    {
        User::factory()->create([
            'email' => 'mismatch@example.com',
            'name' => 'Mismatch User',
        ]);

        $this->artisan('user:reset-password', ['email' => 'mismatch@example.com'])
            ->expectsQuestion('Enter new password for Mismatch User', 'password123')
            ->expectsQuestion('Confirm new password', 'differentpassword')
            ->expectsOutput('Passwords do not match.')
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_when_prompted_password_is_empty(): void
    {
        User::factory()->create([
            'email' => 'empty@example.com',
            'name' => 'Empty User',
        ]);

        $this->artisan('user:reset-password', ['email' => 'empty@example.com'])
            ->expectsQuestion('Enter new password for Empty User', '')
            ->expectsOutput('Password cannot be empty.')
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_when_password_is_too_short(): void
    {
        User::factory()->create(['email' => 'short@example.com']);

        $this->artisan('user:reset-password', [
            'email' => 'short@example.com',
            '--password' => 'short',
        ])
            ->expectsOutput('Password validation failed:')
            ->expectsOutput('  - The password field must be at least 8 characters.')
            ->assertExitCode(1);
    }

    #[Test]
    public function validates_password_length_during_interactive_mode(): void
    {
        User::factory()->create([
            'email' => 'validate@example.com',
            'name' => 'Validate User',
        ]);

        $this->artisan('user:reset-password', ['email' => 'validate@example.com'])
            ->expectsQuestion('Enter new password for Validate User', 'short')
            ->expectsQuestion('Confirm new password', 'short')
            ->expectsOutput('Password validation failed:')
            ->expectsOutput('  - The password field must be at least 8 characters.')
            ->assertExitCode(1);
    }

    #[Test]
    public function successfully_resets_password_with_minimum_length(): void
    {
        $user = User::factory()->create(['email' => 'minimum@example.com']);

        $this->artisan('user:reset-password', [
            'email' => 'minimum@example.com',
            '--password' => '12345678', // Exactly 8 characters
        ])
            ->expectsOutputToContain("Password successfully reset for user: {$user->name} (minimum@example.com)")
            ->assertExitCode(0);

        $user->refresh();
        $this->assertTrue(Hash::check('12345678', $user->password));
    }

    #[Test]
    public function handles_special_characters_in_password(): void
    {
        $user = User::factory()->create(['email' => 'special@example.com']);

        $complexPassword = 'P@ssw0rd!#$%';

        $this->artisan('user:reset-password', [
            'email' => 'special@example.com',
            '--password' => $complexPassword,
        ])
            ->expectsOutputToContain("Password successfully reset for user: {$user->name} (special@example.com)")
            ->assertExitCode(0);

        $user->refresh();
        $this->assertTrue(Hash::check($complexPassword, $user->password));
    }

    #[Test]
    public function preserves_other_user_data(): void
    {
        $user = User::factory()->create([
            'email' => 'preserve@example.com',
            'name' => 'Preserve User',
            'email_verified_at' => now(),
        ]);

        $originalName = $user->name;
        $originalEmail = $user->email;
        $originalVerifiedAt = $user->email_verified_at;

        $this->artisan('user:reset-password', [
            'email' => 'preserve@example.com',
            '--password' => 'newpassword123',
        ])
            ->assertExitCode(0);

        $user->refresh();
        $this->assertSame($originalName, $user->name);
        $this->assertSame($originalEmail, $user->email);
        $this->assertEquals($originalVerifiedAt, $user->email_verified_at);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
