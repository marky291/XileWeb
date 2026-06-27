<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class WikiWebhookController extends Controller
{
    /**
     * GitHub push webhook: verify signature, pull the server's content repo,
     * and invalidate the wiki search index so it rebuilds on next request.
     */
    public function handle(Request $request, string $server)
    {
        abort_unless(config("wiki.servers.{$server}") !== null, 404);

        $secret = config('wiki.webhook_secret');
        abort_if(empty($secret), 503, 'Webhook secret not configured');

        // Verify GitHub's HMAC-SHA256 over the raw body (constant-time compare).
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        abort_unless(hash_equals($expected, (string) $request->header('X-Hub-Signature-256')), 403, 'Invalid signature');

        // Only react to push events; acknowledge everything else (e.g. ping).
        if ($request->header('X-GitHub-Event') !== 'push') {
            return response()->json(['status' => 'ignored', 'event' => $request->header('X-GitHub-Event')]);
        }

        // Only pull when the configured branch was pushed (XileRO=stable,
        // XileRetro=master) — ignore pushes to other branches.
        $branch = config("wiki.servers.{$server}.branch", 'master');
        $ref = (string) $request->input('ref');
        if ($ref !== '' && $ref !== "refs/heads/{$branch}") {
            return response()->json(['status' => 'ignored-branch', 'ref' => $ref, 'expected' => "refs/heads/{$branch}"]);
        }

        $repo = config("wiki.servers.{$server}.repo");
        abort_if(empty($repo) || ! is_dir($repo), 500, 'Content repo dir not configured');

        $result = Process::path($repo)->timeout(120)->run('git pull --ff-only');
        Cache::forget('wiki.search.index');

        if (! $result->successful()) {
            Log::warning("wiki webhook: git pull failed for {$server}", [
                'output' => $result->output(),
                'error' => $result->errorOutput(),
            ]);

            return response()->json(['status' => 'pull-failed', 'server' => $server], 500);
        }

        return response()->json([
            'status' => 'pulled',
            'server' => $server,
            'output' => trim($result->output()),
        ]);
    }
}
