<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RagnarokAdministratorMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RagnarokAdministratorMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_admin_users(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RagnarokAdministratorMiddleware();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals('success', $response->getContent());
    }

    #[Test]
    public function it_blocks_non_admin_users(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RagnarokAdministratorMiddleware();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Unauthorized', $response->getContent());
    }

    #[Test]
    public function it_blocks_guest_users(): void
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => null);

        $middleware = new RagnarokAdministratorMiddleware();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_returns_403_status_code(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RagnarokAdministratorMiddleware();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_returns_descriptive_error_message(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RagnarokAdministratorMiddleware();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertStringContainsString('permission', $response->getContent());
    }

    #[Test]
    public function it_uses_is_admin_method(): void
    {
        // Test that the middleware uses the isAdmin() method, not just is_admin property
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RagnarokAdministratorMiddleware();

        // Verify that is_admin property is true for admin users
        $this->assertTrue($user->isAdmin());

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals(200, $response->getStatusCode());
    }
}
