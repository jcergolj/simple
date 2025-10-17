<?php

namespace Tests\Feature\Console\Commands;

use App\Models\HourlyRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function fails_when_user_already_exists_without_force(): void
    {
        User::factory()->create();

        $this->artisan('app:create-user')
            ->expectsOutput('A user already exists in the system.')
            ->expectsOutput('Only one user is allowed per application.')
            ->expectsOutput('Use --force flag if you want to create another user anyway.')
            ->assertExitCode(1);
    }

    #[Test]
    public function shows_warning_when_creating_with_force_and_existing_user(): void
    {
        User::factory()->create();

        $this->artisan('app:create-user', ['--force' => true])
            ->expectsQuestion('What is your name?', 'John Doe')
            ->expectsQuestion('What is your email address?', 'john@example.com')
            ->expectsQuestion('Enter your password', 'password123')
            ->expectsQuestion('Confirm your password', 'password123')
            ->expectsQuestion('What is your default hourly rate? (Optional, press Enter to skip)', '')
            ->expectsOutput('Warning: A user already exists, but creating another due to --force flag.')
            ->expectsOutput('User created successfully!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    #[Test]
    public function creates_user_successfully_when_no_user_exists(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Jane Smith')
            ->expectsQuestion('What is your email address?', 'jane@example.com')
            ->expectsQuestion('Enter your password', 'securepass123')
            ->expectsQuestion('Confirm your password', 'securepass123')
            ->expectsQuestion('What is your default hourly rate? (Optional, press Enter to skip)', '')
            ->expectsOutput('User created successfully!')
            ->expectsOutput('🎉 Your account is ready!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

    #[Test]
    public function creates_user_with_hourly_rate(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Bob Developer')
            ->expectsQuestion('What is your email address?', 'bob@example.com')
            ->expectsQuestion('Enter your password', 'password123')
            ->expectsQuestion('Confirm your password', 'password123')
            ->expectsQuestion('What is your default hourly rate? (Optional, press Enter to skip)', '75.50')
            ->expectsChoice('Select currency', 'EUR', ['USD' => '$ (USD)', 'EUR' => '€ (EUR)', 'GBP' => '£ (GBP)', 'JPY' => '¥ (JPY)', 'CAD' => 'C$ (CAD)', 'AUD' => 'A$ (AUD)', 'CHF' => 'CHF (CHF)', 'CNY' => '¥ (CNY)', 'SGD' => 'S$ (SGD)', 'HKD' => 'HK$ (HKD)'])
            ->expectsOutput('User created successfully!')
            ->assertExitCode(0);

        $user = User::where('email', 'bob@example.com')->first();
        $this->assertNotNull($user);

        $hourlyRate = HourlyRate::where('rateable_id', $user->id)
            ->where('rateable_type', User::class)
            ->first();

        $this->assertNotNull($hourlyRate);
        $this->assertSame(7550, $hourlyRate->rate->amount); // 75.50 * 100
        $this->assertSame('EUR', $hourlyRate->rate->currency->value);
    }

    #[Test]
    public function fails_with_invalid_email(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Test User')
            ->expectsQuestion('What is your email address?', 'invalid-email')
            ->expectsOutput('Invalid email or email already exists: The email field must be a valid email address.')
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->artisan('app:create-user', ['--force' => true])
            ->expectsQuestion('What is your name?', 'Test User')
            ->expectsQuestion('What is your email address?', 'existing@example.com')
            ->expectsOutput('Invalid email or email already exists: The email has already been taken.')
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_when_passwords_do_not_match(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Test User')
            ->expectsQuestion('What is your email address?', 'test@example.com')
            ->expectsQuestion('Enter your password', 'password123')
            ->expectsQuestion('Confirm your password', 'differentpassword')
            ->expectsOutput('Passwords do not match.')
            ->assertExitCode(1);
    }

    #[Test]
    public function fails_with_short_password(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Test User')
            ->expectsQuestion('What is your email address?', 'test@example.com')
            ->expectsQuestion('Enter your password', 'short')
            ->expectsQuestion('Confirm your password', 'short')
            ->expectsOutput('Password must be at least 8 characters long.')
            ->assertExitCode(1);
    }

    #[Test]
    public function skips_hourly_rate_when_zero_or_negative(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Free Worker')
            ->expectsQuestion('What is your email address?', 'free@example.com')
            ->expectsQuestion('Enter your password', 'password123')
            ->expectsQuestion('Confirm your password', 'password123')
            ->expectsQuestion('What is your default hourly rate? (Optional, press Enter to skip)', '0')
            ->expectsOutput('User created successfully!')
            ->assertExitCode(0);

        $user = User::where('email', 'free@example.com')->first();
        $this->assertNull($user->hourlyRate);
    }

    #[Test]
    public function displays_user_table_after_creation(): void
    {
        $this->artisan('app:create-user')
            ->expectsQuestion('What is your name?', 'Table User')
            ->expectsQuestion('What is your email address?', 'table@example.com')
            ->expectsQuestion('Enter your password', 'password123')
            ->expectsQuestion('Confirm your password', 'password123')
            ->expectsQuestion('What is your default hourly rate? (Optional, press Enter to skip)', '100')
            ->expectsChoice('Select currency', 'USD', ['USD' => '$ (USD)', 'EUR' => '€ (EUR)', 'GBP' => '£ (GBP)', 'JPY' => '¥ (JPY)', 'CAD' => 'C$ (CAD)', 'AUD' => 'A$ (AUD)', 'CHF' => 'CHF (CHF)', 'CNY' => '¥ (CNY)', 'SGD' => 'S$ (SGD)', 'HKD' => 'HK$ (HKD)'])
            ->expectsOutput('User created successfully!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'Table User',
            'email' => 'table@example.com',
        ]);
    }
}
