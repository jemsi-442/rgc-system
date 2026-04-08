<?php

namespace Tests\Feature\Operations;

use Tests\TestCase;

class OpsBackupChecklistTest extends TestCase
{
    public function test_backup_checklist_reports_public_uploads_as_required(): void
    {
        config([
            'filesystems.default' => 'public',
        ]);

        $this->artisan('ops:backup-checklist')
            ->expectsOutputToContain('[REQUIRED] UPLOADS')
            ->expectsOutputToContain('storage/app/public')
            ->assertExitCode(0);
    }

    public function test_backup_checklist_json_output_includes_environment(): void
    {
        $this->artisan('ops:backup-checklist --json')
            ->expectsOutputToContain('"environment"')
            ->expectsOutputToContain('"items"')
            ->assertExitCode(0);
    }
}
