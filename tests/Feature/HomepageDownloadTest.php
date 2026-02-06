<?php

namespace Tests\Feature;

use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guests_see_login_prompt_instead_of_downloads(): void
    {
        Download::factory()->full()->create(['name' => 'Test Full Client']);
        Download::factory()->android()->create(['name' => 'Test Android APK']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Download XileRetro');
        $response->assertSee('Sign in to unlock');
        $response->assertDontSee('Test Full Client');
        $response->assertDontSee('Test Android APK');
    }

    public function test_authenticated_users_see_download_links(): void
    {
        $user = User::factory()->create();
        Download::factory()->full()->create(['name' => 'Test Full Client']);
        Download::factory()->android()->create(['name' => 'Test Android APK']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Account Required');
        $response->assertSee('Test Full Client');
        $response->assertSee('Test Android APK');
    }

    public function test_item_database_shows_login_prompt_for_guests(): void
    {
        $response = $this->get('/item-database');

        $response->assertStatus(200);
        $response->assertSee('Item Database');
        $response->assertSee('Login Required');
    }

    public function test_item_database_shows_content_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/item-database');

        $response->assertStatus(200);
        $response->assertSee('Item Database');
        $response->assertDontSee('Login Required');
        $response->assertSee('Search by name');
    }
}
