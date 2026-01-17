<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscordAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_is_redirected_to_discord(): void
    {
        Socialite::fake('discord');

        $response = $this->get(route('auth.discord.redirect'));

        $response->assertRedirect();
    }

    #[Test]
    public function new_user_is_created_from_discord(): void
    {
        Socialite::fake('discord', (new SocialiteUser)->map([
            'id' => 'discord-123456',
            'name' => 'Test User',
            'email' => 'discord@example.com',
            'nickname' => 'testuser#1234',
        ])->setToken('test-token')->setRefreshToken('test-refresh-token'));

        $response = $this->get(route('auth.discord.callback'));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'discord_id' => 'discord-123456',
            'email' => 'discord@example.com',
            'discord_username' => 'testuser#1234',
        ]);

        $this->assertAuthenticated();
    }

    #[Test]
    public function existing_user_with_discord_id_is_logged_in(): void
    {
        $user = User::factory()->create([
            'discord_id' => 'discord-123456',
        ]);

        Socialite::fake('discord', (new SocialiteUser)->map([
            'id' => 'discord-123456',
            'name' => 'Test User',
            'email' => 'discord@example.com',
            'nickname' => 'testuser#1234',
        ])->setToken('new-token')->setRefreshToken('new-refresh-token'));

        $response = $this->get(route('auth.discord.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function existing_user_with_matching_email_links_discord(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'discord_id' => null,
        ]);

        Socialite::fake('discord', (new SocialiteUser)->map([
            'id' => 'discord-789',
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'nickname' => 'testuser#5678',
        ])->setToken('test-token')->setRefreshToken('test-refresh-token'));

        $response = $this->get(route('auth.discord.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertEquals('discord-789', $user->discord_id);
        $this->assertEquals('testuser#5678', $user->discord_username);
    }

    #[Test]
    public function discord_tokens_are_updated_on_login(): void
    {
        $user = User::factory()->create([
            'discord_id' => 'discord-123456',
            'discord_token' => 'old-token',
            'discord_refresh_token' => 'old-refresh-token',
        ]);

        Socialite::fake('discord', (new SocialiteUser)->map([
            'id' => 'discord-123456',
            'name' => 'Test User',
            'email' => 'discord@example.com',
            'nickname' => 'testuser#1234',
        ])->setToken('new-token')->setRefreshToken('new-refresh-token'));

        $this->get(route('auth.discord.callback'));

        $user->refresh();
        $this->assertEquals('new-token', $user->discord_token);
        $this->assertEquals('new-refresh-token', $user->discord_refresh_token);
    }

    #[Test]
    public function login_page_shows_discord_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Continue with Discord');
        $response->assertSee(route('auth.discord.redirect'));
    }

    #[Test]
    public function register_page_shows_discord_button(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Continue with Discord');
        $response->assertSee(route('auth.discord.redirect'));
    }

    #[Test]
    public function authenticated_user_cannot_access_discord_redirect(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('auth.discord.redirect'));

        $response->assertRedirect(route('dashboard'));
    }
}
