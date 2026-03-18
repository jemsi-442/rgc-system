<?php

use App\Models\Announcement;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('announcements:archive-expired', function () {
    $archived = Announcement::query()
        ->whereNull('archived_at')
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->update([
            'archived_at' => now(),
            'is_pinned' => false,
            'pinned_at' => null,
        ]);

    $this->info("Archived {$archived} expired announcements.");
})->purpose('Archive expired announcements and remove them from dashboard surfaces.');

Schedule::command('announcements:archive-expired')->hourly();
