<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendPostToDiscord implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhookUrl = config('services.discord.discord_webhook_news');

        if (empty($webhookUrl)) {
            Log::warning('Discord news webhook URL is not configured');

            return;
        }

        $payload = $this->buildPayload();

        $response = Http::post($webhookUrl, $payload);

        if ($response->failed()) {
            Log::error('Failed to send Discord notification', [
                'post_id' => $this->post->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $this->fail('Discord webhook failed: '.$response->status());
        }
    }

    /**
     * Build the Discord webhook payload.
     *
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $clientLabel = $this->post->client === Post::CLIENT_RETRO ? 'Retro' : 'XileRO';

        $lines = [];

        // Header
        $lines[] = '@everyone';
        $lines[] = '';
        $lines[] = "**{$this->post->title}**";
        $lines[] = "-# {$clientLabel} | ".now()->format('F j, Y');
        $lines[] = '';

        // Article content - truncate at paragraph boundary to preserve formatting
        $content = $this->truncateContent($this->post->article_content, 1400);
        $lines[] = $content;

        // Featured items
        $items = $this->post->items()->limit(10)->get();
        if ($items->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '**Featured Items**';

            $baseUrl = 'https://xilero.net';

            foreach ($items as $item) {
                $itemUrl = "{$baseUrl}/item-database?search=".urlencode($item->name);
                $lines[] = "- `{$item->item_id}` [{$item->name}]({$itemUrl})";
            }
        }

        // Footer
        $lines[] = '';
        $lines[] = '-# Posted by '.($this->post->user?->name ?? 'XileRO Team');

        return [
            'content' => implode("\n", $lines),
            'flags' => 4, // SUPPRESS_EMBEDS - prevents link previews
        ];
    }

    /**
     * Truncate content at a paragraph boundary to preserve markdown formatting.
     */
    private function truncateContent(string $content, int $limit): string
    {
        if (strlen($content) <= $limit) {
            return $content;
        }

        // Try to truncate at a paragraph break (double newline)
        $truncated = substr($content, 0, $limit);
        $lastParagraph = strrpos($truncated, "\n\n");

        if ($lastParagraph !== false && $lastParagraph > $limit * 0.5) {
            return substr($content, 0, $lastParagraph)."\n\n...";
        }

        // Fall back to truncating at a single line break
        $lastLine = strrpos($truncated, "\n");

        if ($lastLine !== false && $lastLine > $limit * 0.7) {
            return substr($content, 0, $lastLine)."\n...";
        }

        // Last resort: truncate at word boundary
        return Str::limit($content, $limit, '...');
    }
}
