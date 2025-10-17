<?php

namespace Tests\Unit\Http\Requests\Settings;

use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Requests\Settings\UpdateProfileRequest::class)]
class UpdateProfileRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function extends_app_form_request(): void
    {
        $request = new UpdateProfileRequest;

        $this->assertInstanceOf(\App\Http\Requests\AppFormRequest::class, $request);
    }

    #[Test]
    public function get_redirect_url_returns_correct_route(): void
    {
        $request = new UpdateProfileRequest;

        // Use reflection to access the protected method
        $reflection = new \ReflectionMethod($request, 'getRedirectUrl');

        $result = $reflection->invoke($request);

        $this->assertSame(route('settings.profile.edit'), $result);
    }

    #[Test]
    public function validates_name_is_required(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make(['email' => 'test@example.com'], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_name_is_string(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make([
            'name' => 123,
            'email' => 'test@example.com',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_name_max_length(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make([
            'name' => str_repeat('a', 256), // 256 characters
            'email' => 'test@example.com',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_email_is_required(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make(['name' => 'John Doe'], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_email_format(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'invalid-email',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_email_max_length(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $longEmail = str_repeat('a', 250).'@example.com'; // Over 255 chars
        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => $longEmail,
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_email_uniqueness(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);
        $anotherUser = User::factory()->create(['email' => 'another@example.com']);

        $request = new UpdateProfileRequest;
        $request->setUserResolver(fn () => $anotherUser);

        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'existing@example.com', // Email already taken by another user
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function allows_same_email_for_current_user(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $request = new UpdateProfileRequest;
        $request->setUserResolver(fn () => $user);

        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'user@example.com', // Same email as current user
        ], $rules);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function validates_email_is_lowercase(): void
    {
        $rules = (new UpdateProfileRequest)->rules();

        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'TEST@EXAMPLE.COM',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function passes_with_valid_data(): void
    {
        $user = User::factory()->create();

        $request = new UpdateProfileRequest;
        $request->setUserResolver(fn () => $user);

        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], $rules);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function has_custom_error_messages(): void
    {
        $request = new UpdateProfileRequest;
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
    }

    #[Test]
    public function custom_messages_are_strings(): void
    {
        $request = new UpdateProfileRequest;
        $messages = $request->messages();

        foreach ($messages as $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }
}
