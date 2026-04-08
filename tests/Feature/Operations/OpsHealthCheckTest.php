<?php

namespace Tests\Feature\Operations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpsHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_ops_health_check_passes_for_testing_defaults(): void
    {
        $this->artisan('ops:health-check')
            ->expectsOutputToContain('Operations health check for environment: testing')
            ->assertExitCode(0);
    }

    public function test_ops_health_check_fails_when_api_token_expiry_is_invalid(): void
    {
        config(['auth.api_token_expire_minutes' => 0]);

        $this->artisan('ops:health-check')
            ->expectsOutputToContain('[FAIL] api_token_expiry')
            ->assertExitCode(1);
    }

    public function test_ops_health_check_fails_for_production_log_mailer(): void
    {
        config([
            'app.env' => 'production',
            'mail.default' => 'log',
        ]);

        $this->artisan('ops:health-check')
            ->expectsOutputToContain('[FAIL] mail_mailer')
            ->assertExitCode(1);
    }

    public function test_ops_health_check_reports_public_storage_link_missing(): void
    {
        config([
            'filesystems.default' => 'public',
            'filesystems.links' => [
                base_path('bootstrap/cache/missing-storage-link') => storage_path('app/public'),
            ],
        ]);

        $this->artisan('ops:health-check')
            ->expectsOutputToContain('[FAIL] storage_link')
            ->assertExitCode(1);
    }
}
