<?php

namespace Tests\Feature;

use App\Jobs\SendPostToDiscord;
use App\Models\Item;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SendPostToDiscordTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_discord_webhook_on_post_creation(): void
    {
        Http::fake();
        config(['services.discord.discord_webhook_news' => 'https://discord.com/api/webhooks/test']);

        $post = Post::factory()->create([
            'title' => 'Test Post Title',
            'article_content' => 'This is the article content.',
            'client' => Post::CLIENT_XILERO,
        ]);

        $job = new SendPostToDiscord($post);
        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://discord.com/api/webhooks/test'
                && str_contains($request->data()['content'], 'Test Post Title')
                && str_contains($request->data()['content'], 'This is the article content.');
        });
    }

    public function test_does_not_send_when_webhook_not_configured(): void
    {
        Http::fake();
        Log::shouldReceive('warning')
            ->once()
            ->with('Discord news webhook URL is not configured');

        config(['services.discord.discord_webhook_news' => null]);

        $post = Post::factory()->create();

        $job = new SendPostToDiscord($post);
        $job->handle();

        Http::assertNothingSent();
    }

    public function test_message_contains_xilero_label(): void
    {
        Http::fake();
        config(['services.discord.discord_webhook_news' => 'https://discord.com/api/webhooks/test']);

        $post = Post::factory()->create([
            'client' => Post::CLIENT_XILERO,
        ]);

        $job = new SendPostToDiscord($post);
        $job->handle();

        Http::assertSent(function ($request) {
            return str_contains($request->data()['content'], 'XileRO |');
        });
    }

    public function test_message_contains_retro_label(): void
    {
        Http::fake();
        config(['services.discord.discord_webhook_news' => 'https://discord.com/api/webhooks/test']);

        $post = Post::factory()->create([
            'client' => Post::CLIENT_RETRO,
        ]);

        $job = new SendPostToDiscord($post);
        $job->handle();

        Http::assertSent(function ($request) {
            return str_contains($request->data()['content'], 'Retro |');
        });
    }

    public function test_featured_items_included_in_message(): void
    {
        Http::fake();
        config(['services.discord.discord_webhook_news' => 'https://discord.com/api/webhooks/test']);

        $post = Post::factory()->create([
            'client' => Post::CLIENT_XILERO,
        ]);

        $item = Item::factory()->create([
            'item_id' => 12345,
            'name' => 'Test Item',
            'is_xileretro' => false,
        ]);

        $post->items()->attach($item->id);

        $job = new SendPostToDiscord($post);
        $job->handle();

        Http::assertSent(function ($request) {
            $content = $request->data()['content'];

            return str_contains($content, 'Featured Items')
                && str_contains($content, '12345')
                && str_contains($content, 'Test Item');
        });
    }
}
