<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ServerOwner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServerOwnerMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithGroupId(int $groupId): User
    {
        $user = User::factory()->create();
        // Set group_id as a dynamic property
        $user->group_id = $groupId;

        return $user;
    }

    #[Test]
    public function it_allows_group_id_99_users(): void
    {
        $user = $this->createUserWithGroupId(99);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $middleware = new ServerOwner();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertEquals('success', $response->getContent());
    }

    #[Test]
    public function it_redirects_non_owner_users(): void
    {
        $user = $this->createUserWithGroupId(1);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $middleware = new ServerOwner();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertTrue($response->isRedirect(route('home')));
    }

    #[Test]
    public function it_redirects_group_id_zero_users(): void
    {
        $user = $this->createUserWithGroupId(0);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $middleware = new ServerOwner();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertTrue($response->isRedirect(route('home')));
    }

    #[Test]
    public function it_only_allows_exactly_99(): void
    {
        $user = $this->createUserWithGroupId(98);
        $this->actingAs($user);

        $request = Request::create('/test', 'GET');
        $middleware = new ServerOwner();

        $response = $middleware->handle($request, fn ($req) => response('success'));

        $this->assertTrue($response->isRedirect(route('home')));
    }
}
