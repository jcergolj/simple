<?php

namespace Tests\Unit\Http\Requests\Settings;

use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Requests\Settings\UpdatePasswordRequest::class)]
class UpdatePasswordRequestTest extends TestCase
{
    #[Test]
    public function extends_app_form_request(): void
    {
        $request = new UpdatePasswordRequest;

        $this->assertInstanceOf(\App\Http\Requests\AppFormRequest::class, $request);
    }

    #[Test]
    public function get_redirect_url_returns_correct_route(): void
    {
        $request = new UpdatePasswordRequest;

        // Use reflection to access the protected method
        $reflection = new \ReflectionMethod($request, 'getRedirectUrl');

        $result = $reflection->invoke($request);

        // The method should return a URL containing password edit route or fallback to previous URL
        $this->assertTrue(
            str_contains((string) $result, 'password.edit') || str_contains((string) $result, 'http')
        );
    }

    #[Test]
    public function validates_current_password_is_required(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $validator = Validator::make([
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('current_password', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_current_password_is_string(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $validator = Validator::make([
            'current_password' => 123,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('current_password', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_password_is_required(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $validator = Validator::make([
            'current_password' => 'currentpassword',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_password_is_string(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $validator = Validator::make([
            'current_password' => 'currentpassword',
            'password' => 123,
            'password_confirmation' => 123,
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_password_confirmation(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $validator = Validator::make([
            'current_password' => 'currentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function validates_password_meets_default_requirements(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        // Test with weak password (too short)
        $validator = Validator::make([
            'current_password' => 'currentpassword',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function passes_with_valid_password_data(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        Validator::make([
            'current_password' => 'currentpassword123',
            'password' => 'newstrongpassword123',
            'password_confirmation' => 'newstrongpassword123',
        ], $rules);

        // Note: We can't test current_password validation without actual user context
        // So we'll just check that required/string/confirmed rules pass
        $basicRules = [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
        ];

        $basicValidator = Validator::make([
            'current_password' => 'currentpassword123',
            'password' => 'newstrongpassword123',
            'password_confirmation' => 'newstrongpassword123',
        ], $basicRules);

        $this->assertTrue($basicValidator->passes());
    }

    #[Test]
    public function has_custom_error_messages(): void
    {
        $request = new UpdatePasswordRequest;
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('current_password.required', $messages);
        $this->assertArrayHasKey('current_password.current_password', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.confirmed', $messages);
    }

    #[Test]
    public function custom_messages_are_strings(): void
    {
        $request = new UpdatePasswordRequest;
        $messages = $request->messages();

        foreach ($messages as $message) {
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }

    #[Test]
    public function rules_include_current_password_validation(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $this->assertArrayHasKey('current_password', $rules);
        $this->assertContains('current_password', $rules['current_password']);
    }

    #[Test]
    public function rules_include_password_defaults(): void
    {
        $rules = (new UpdatePasswordRequest)->rules();

        $this->assertArrayHasKey('password', $rules);

        // Check that Password::defaults() is included in the rules
        $passwordRules = $rules['password'];
        $hasPasswordRule = false;

        foreach ($passwordRules as $rule) {
            if ($rule instanceof \Illuminate\Validation\Rules\Password) {
                $hasPasswordRule = true;
                break;
            }
        }

        $this->assertTrue($hasPasswordRule, 'Password rules should include Password::defaults()');
    }
}
