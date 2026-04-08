<?php

use App\Models\Announcement;
use App\Models\BranchMessage;
use App\Models\HomeSlider;
use App\Models\SystemAssistantInteraction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

Artisan::command('assistant:prune-interactions {--days=365} {--guest-days=30}', function () {
    $retentionDays = max(1, (int) $this->option('days'));
    $guestRetentionDays = max(1, (int) $this->option('guest-days'));

    $deletedGuest = SystemAssistantInteraction::query()
        ->whereNull('user_id')
        ->where('created_at', '<', now()->subDays($guestRetentionDays))
        ->delete();

    $deletedAll = SystemAssistantInteraction::query()
        ->where('created_at', '<', now()->subDays($retentionDays))
        ->delete();

    $this->info('Pruned ' . ($deletedGuest + $deletedAll) . ' assistant interactions.');
})->purpose('Prune old assistant interaction logs, with shorter retention for anonymous traffic.');

Schedule::command('assistant:prune-interactions')->daily();

Artisan::command('media:prune-orphans {--dry-run}', function () {
    $disk = Storage::disk('public');
    $directories = ['sliders', 'announcements', 'branch-messages'];

    $storedPaths = collect($directories)
        ->flatMap(fn (string $directory) => $disk->allFiles($directory))
        ->filter()
        ->unique()
        ->values();

    $referencedPaths = collect();

    HomeSlider::query()
        ->whereNotNull('image_path')
        ->pluck('image_path')
        ->filter()
        ->each(fn (string $path) => $referencedPaths->push($path));

    Announcement::query()
        ->whereNotNull('image_path')
        ->pluck('image_path')
        ->filter()
        ->each(fn (string $path) => $referencedPaths->push($path));

    BranchMessage::query()
        ->select(['id', 'attachment_path', 'attachment_name', 'attachment_mime_type', 'attachment_size', 'attachments'])
        ->lazyById()
        ->each(function (BranchMessage $message) use ($referencedPaths): void {
            foreach ($message->attachmentItems() as $attachment) {
                $path = (string) ($attachment['path'] ?? '');

                if ($path !== '') {
                    $referencedPaths->push($path);
                }
            }
        });

    $orphans = $storedPaths
        ->diff($referencedPaths->filter()->unique()->values())
        ->values();

    if ($orphans->isEmpty()) {
        $this->info('No orphaned public uploads were found.');
        return;
    }

    if ($this->option('dry-run')) {
        $this->info('Dry run: found ' . $orphans->count() . ' orphaned public uploads.');

        foreach ($orphans->take(20) as $path) {
            $this->line($path);
        }

        if ($orphans->count() > 20) {
            $this->line('...and ' . ($orphans->count() - 20) . ' more.');
        }

        return;
    }

    $deleted = 0;

    foreach ($orphans as $path) {
        if ($disk->delete($path)) {
            $deleted++;
        }
    }

    $this->info('Deleted ' . $deleted . ' orphaned public uploads.');
})->purpose('Delete unreferenced slider, announcement, and branch chat uploads from the public disk.');

Schedule::command('media:prune-orphans')->daily();

Artisan::command('ops:health-check {--json}', function () {
    $checks = collect();
    $environment = (string) Config::get('app.env', app()->environment());
    $isProduction = $environment === 'production';

    $record = function (string $name, string $status, string $message) use ($checks): void {
        $checks->push([
            'name' => $name,
            'status' => $status,
            'message' => $message,
        ]);
    };

    $record('app_key', filled(config('app.key')) ? 'ok' : 'fail', filled(config('app.key'))
        ? 'APP_KEY is configured.'
        : 'APP_KEY is missing.');

    $record('app_debug', ! $isProduction || ! config('app.debug') ? 'ok' : 'fail', ! $isProduction || ! config('app.debug')
        ? 'APP_DEBUG is acceptable for this environment.'
        : 'APP_DEBUG must be false in production.');

    $record('app_url', ! $isProduction || str_starts_with((string) config('app.url'), 'https://') ? 'ok' : 'fail', ! $isProduction || str_starts_with((string) config('app.url'), 'https://')
        ? 'APP_URL is acceptable for this environment.'
        : 'APP_URL should use HTTPS in production.');

    $record('session_secure_cookie', ! $isProduction || (bool) config('session.secure') ? 'ok' : 'fail', ! $isProduction || (bool) config('session.secure')
        ? 'Session secure cookie setting is acceptable.'
        : 'SESSION_SECURE_COOKIE should be enabled in production.');

    $record('session_driver', ! $isProduction || config('session.driver') !== 'array' ? 'ok' : 'fail', ! $isProduction || config('session.driver') !== 'array'
        ? 'Session driver is acceptable.'
        : 'SESSION_DRIVER=array is not suitable for production.');

    $record('cache_store', ! $isProduction || config('cache.default') !== 'array' ? 'ok' : 'fail', ! $isProduction || config('cache.default') !== 'array'
        ? 'Cache store is acceptable.'
        : 'CACHE_STORE=array is not suitable for production.');

    $record('queue_connection', ! $isProduction || ! in_array(config('queue.default'), ['sync', 'null'], true) ? 'ok' : 'fail', ! $isProduction || ! in_array(config('queue.default'), ['sync', 'null'], true)
        ? 'Queue connection is acceptable.'
        : 'QUEUE_CONNECTION should be asynchronous in production.');

    $record('api_token_expiry', (int) config('auth.api_token_expire_minutes', 0) > 0 ? 'ok' : 'fail', (int) config('auth.api_token_expire_minutes', 0) > 0
        ? 'API token expiry is configured.'
        : 'AUTH_API_TOKEN_EXPIRE_MINUTES must be greater than zero.');

    $record('mail_mailer', ! $isProduction || ! in_array(config('mail.default'), ['log', 'array'], true) ? 'ok' : 'fail', ! $isProduction || ! in_array(config('mail.default'), ['log', 'array'], true)
        ? 'Mailer is acceptable for this environment.'
        : 'MAIL_MAILER should point to a real delivery provider in production.');

    $record('filesystem_disk', ! $isProduction || in_array(config('filesystems.default'), ['public', 's3'], true) ? 'ok' : 'warn', ! $isProduction || in_array(config('filesystems.default'), ['public', 's3'], true)
        ? 'Filesystem disk is acceptable.'
        : 'Production uploads are safest on public+persistent volume or s3.');

    $publicDiskRoot = realpath((string) config('filesystems.disks.public.root', storage_path('app/public')));
    $publicLinkPaths = array_keys(config('filesystems.links', [
        public_path('storage') => storage_path('app/public'),
    ]));
    $publicStorageLinked = collect($publicLinkPaths)->contains(function (string $linkPath) use ($publicDiskRoot): bool {
        $resolvedLink = realpath($linkPath);

        return $publicDiskRoot !== false
            && $resolvedLink !== false
            && $resolvedLink === $publicDiskRoot;
    });

    $record('storage_link', config('filesystems.default') !== 'public' || $publicStorageLinked ? 'ok' : 'fail', config('filesystems.default') !== 'public' || $publicStorageLinked
        ? 'Public upload path is accessible.'
        : 'Run php artisan storage:link so public uploads are reachable.');

    $databaseChecks = [
        'sessions' => config('session.driver') === 'database',
        'cache' => config('cache.default') === 'database',
        'jobs' => config('queue.default') === 'database',
        'failed_jobs' => str_starts_with((string) config('queue.failed.driver'), 'database'),
    ];

    foreach ($databaseChecks as $table => $required) {
        if (! $required) {
            $record('table_' . $table, 'ok', strtoupper($table) . ' table is not required by the current config.');
            continue;
        }

        try {
            $record('table_' . $table, Schema::hasTable($table) ? 'ok' : 'fail', Schema::hasTable($table)
                ? strtoupper($table) . ' table exists.'
                : strtoupper($table) . ' table is missing.');
        } catch (\Throwable $exception) {
            $record('table_' . $table, 'fail', 'Could not verify ' . $table . ' table: ' . $exception->getMessage());
        }
    }

    if ($this->option('json')) {
        $this->line(json_encode([
            'environment' => $environment,
            'checks' => $checks->all(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('Operations health check for environment: ' . $environment);

        foreach ($checks as $check) {
            $label = match ($check['status']) {
                'ok' => '[OK] ',
                'warn' => '[WARN] ',
                default => '[FAIL] ',
            };

            $this->line($label . $check['name'] . ' - ' . $check['message']);
        }
    }

    $hasFailure = $checks->contains(fn (array $check) => $check['status'] === 'fail');

    return $hasFailure ? 1 : 0;
})->purpose('Check production-critical config, token expiry, and required runtime tables.');

Artisan::command('ops:backup-checklist {--json}', function () {
    $environment = (string) Config::get('app.env', app()->environment());
    $filesystemDisk = (string) config('filesystems.default', 'local');
    $databaseDriver = (string) config('database.default', 'unknown');
    $databaseName = (string) config('database.connections.' . $databaseDriver . '.database', 'unknown');

    $items = collect([
        [
            'category' => 'database',
            'status' => 'required',
            'message' => 'Back up the ' . $databaseDriver . ' database used by environment "' . $environment . '" (' . $databaseName . ').',
        ],
        [
            'category' => 'secrets',
            'status' => 'required',
            'message' => 'Back up runtime secrets outside the app server: APP_KEY, DB credentials, MAIL credentials, and SNIPPE credentials.',
        ],
        [
            'category' => 'uploads',
            'status' => $filesystemDisk === 'public' ? 'required' : 'review',
            'message' => match ($filesystemDisk) {
                'public' => 'Back up the persistent upload volume behind storage/app/public together with the database.',
                's3' => 'Verify bucket versioning or external backup coverage for the configured S3-compatible storage.',
                default => 'Review how the "' . $filesystemDisk . '" disk persists user uploads before go-live.',
            },
        ],
        [
            'category' => 'restore',
            'status' => 'required',
            'message' => 'Test at least one full restore in staging, including database, uploads, storage link, queue worker, and scheduler startup.',
        ],
    ]);

    if ($this->option('json')) {
        $this->line(json_encode([
            'environment' => $environment,
            'items' => $items->all(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    $this->info('Backup checklist for environment: ' . $environment);

    foreach ($items as $item) {
        $label = match ($item['status']) {
            'required' => '[REQUIRED] ',
            default => '[REVIEW] ',
        };

        $this->line($label . strtoupper($item['category']) . ' - ' . $item['message']);
    }

    return self::SUCCESS;
})->purpose('Summarize what must be backed up and restore-tested for the current deployment config.');
