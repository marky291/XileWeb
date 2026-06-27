<?php

namespace Tests\Feature\Wiki;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class WikiWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['wiki.webhook_secret' => 'testsecret']);
        config(['wiki.servers.xilero.repo' => sys_get_temp_dir()]); // an existing dir
        config(['wiki.servers.xilero.branch' => 'main']);
    }

    private function sig(string $payload, string $secret = 'testsecret'): string
    {
        return 'sha256=' . hash_hmac('sha256', $payload, $secret);
    }

    private function send(string $server, string $payload, array $headers)
    {
        return $this->call('POST', "/webhooks/wiki/{$server}", [], [], [], array_merge(
            ['CONTENT_TYPE' => 'application/json'],
            $headers
        ), $payload);
    }

    public function test_rejects_invalid_signature(): void
    {
        $this->send('xilero', '{}', [
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=deadbeef',
            'HTTP_X_GITHUB_EVENT' => 'push',
        ])->assertForbidden();
    }

    public function test_ignores_non_push_event(): void
    {
        Process::fake();
        $payload = '{}';
        $this->send('xilero', $payload, [
            'HTTP_X_HUB_SIGNATURE_256' => $this->sig($payload),
            'HTTP_X_GITHUB_EVENT' => 'ping',
        ])->assertOk()->assertJson(['status' => 'ignored']);
        Process::assertNothingRan();
    }

    public function test_push_pulls_repo_and_returns_ok(): void
    {
        Process::fake(['git pull*' => Process::result('Already up to date.')]);
        $payload = '{"ref":"refs/heads/main"}';
        $this->send('xilero', $payload, [
            'HTTP_X_HUB_SIGNATURE_256' => $this->sig($payload),
            'HTTP_X_GITHUB_EVENT' => 'push',
        ])->assertOk()->assertJson(['status' => 'pulled', 'server' => 'xilero']);
        Process::assertRan(fn ($process) => str_contains($process->command, 'git pull'));
    }

    public function test_ignores_push_to_other_branch(): void
    {
        Process::fake();
        $payload = '{"ref":"refs/heads/dev"}'; // configured branch is 'main'
        $this->send('xilero', $payload, [
            'HTTP_X_HUB_SIGNATURE_256' => $this->sig($payload),
            'HTTP_X_GITHUB_EVENT' => 'push',
        ])->assertOk()->assertJson(['status' => 'ignored-branch']);
        Process::assertNothingRan();
    }

    public function test_unknown_server_404s(): void
    {
        $payload = '{}';
        $this->send('nope', $payload, [
            'HTTP_X_HUB_SIGNATURE_256' => $this->sig($payload),
            'HTTP_X_GITHUB_EVENT' => 'push',
        ])->assertNotFound();
    }
}
