<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password {email} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user\'s password by email address';

    /** Execute the console command. */
    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->option('password');

        // Find the user
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return self::FAILURE;
        }

        // If password not provided, prompt for it
        if (! $password) {
            $password = $this->secret('Enter new password for '.$user->name);

            if (! $password) {
                $this->error('Password cannot be empty.');

                return self::FAILURE;
            }

            $confirmPassword = $this->secret('Confirm new password');

            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match.');

                return self::FAILURE;
            }
        }

        // Validate password
        $validator = Validator::make(['password' => $password], [
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Password validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - '.$error);
            }

            return self::FAILURE;
        }

        // Update the password
        $user->update([
            'password' => Hash::make($password),
        ]);

        $this->components->info("Password successfully reset for user: {$user->name} ({$user->email})");

        return self::SUCCESS;
    }
}
