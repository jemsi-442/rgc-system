<?php

use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;
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

Artisan::command('mail:test {email}', function (string $email) {
    Mail::raw(
        "RGC SMTP test email. If you received this message, outbound mail is configured correctly.",
        function ($message) use ($email): void {
            $message
                ->to($email)
                ->subject('RGC SMTP Test');
        }
    );

    $this->info("Test email sent to {$email}.");
})->purpose('Send a test email using the current mailer configuration.');