<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\SingleUserRestriction;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Middleware\SingleUserRestriction::class)]
class SingleUserRestrictionTest extends TestCase
{
    private SingleUserRestriction $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SingleUserRestriction;
    }

    #[Test]
    public function allows_request_when_no_users_exist(\Illuminate\Http\Request $request): void
    {
        // Ensure no users exist
        User::query()->delete();

        $request = $request->create('/register', 'GET');
        $nextCalled = false;

        $response = $this->middleware->handle($request, function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('OK');
        });

        $this->assertTrue($nextCalled);
        $this->assertSame('OK', $response->getContent());
    }

    #[Test]
    public function blocks_request_with_json_response_when_user_exists_and_expects_json(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/register', 'POST');
        $request->headers->set('Accept', 'application/json');

        $nextCalled = false;

        $response = $this->middleware->handle($request, function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('OK');
        });

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertStringContainsString('Registration is disabled', (string) $data['message']);
    }

    #[Test]
    public function blocks_request_with_redirect_when_user_exists_and_expects_html(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/register', 'GET');

        $nextCalled = false;

        $response = $this->middleware->handle($request, function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('OK');
        });

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function handle_method_signature_is_correct(): void
    {
        $reflection = new \ReflectionMethod(SingleUserRestriction::class, 'handle');

        $this->assertTrue($reflection->isPublic());

        $parameters = $reflection->getParameters();
        $this->assertCount(2, $parameters);

        $this->assertSame('request', $parameters[0]->getName());
        $this->assertSame(Request::class, $parameters[0]->getType()->getName());

        $this->assertSame('next', $parameters[1]->getName());
        $this->assertSame(Closure::class, $parameters[1]->getType()->getName());

        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionType::class, $returnType);
        $this->assertSame(\Symfony\Component\HttpFoundation\Response::class, $returnType->getName());
    }

    #[Test]
    public function returns_correct_json_error_message(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/api/register', 'POST');
        $request->headers->set('Accept', 'application/json');

        $response = $this->middleware->handle($request, fn ($request) => response('Should not reach here'));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);

        $this->assertSame([
            'message' => 'Registration is disabled. Only one user is allowed per application.',
        ], $data);
    }

    #[Test]
    public function respects_accept_json_header(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/register', 'POST');
        $request->headers->set('Accept', 'application/json');

        $response = $this->middleware->handle($request, fn ($request) => response('Should not reach here'));

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    #[Test]
    public function handles_xhr_requests_without_json_accept_header(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/register', 'POST');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $response = $this->middleware->handle($request, fn ($request) => response('Should not reach here'));

        // XHR requests without explicit JSON accept header will get redirect
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    #[Test]
    public function works_with_multiple_users(\Illuminate\Http\Request $request): void
    {
        User::factory()->count(3)->create();

        $request = $request->create('/register', 'GET');

        $nextCalled = false;

        $response = $this->middleware->handle($request, function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('OK');
        });

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    #[Test]
    public function allows_other_routes_when_users_exist(\Illuminate\Http\Request $request): void
    {
        User::factory()->create();

        $request = $request->create('/dashboard', 'GET');

        $nextCalled = false;

        $response = $this->middleware->handle($request, function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('Dashboard');
        });

        // Note: This middleware is meant to be applied only to registration routes
        // but technically it will block any route if a user exists
        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    #[Test]
    public function middleware_can_be_instantiated(): void
    {
        $middleware = new SingleUserRestriction;

        $this->assertInstanceOf(SingleUserRestriction::class, $middleware);
    }
}
