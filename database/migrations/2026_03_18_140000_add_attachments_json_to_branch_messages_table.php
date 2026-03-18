<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            $table->json('attachments')->nullable()->after('attachment_size');
        });

        DB::table('branch_messages')
            ->whereNotNull('attachment_path')
            ->orderBy('id')
            ->chunkById(100, function ($messages): void {
                foreach ($messages as $message) {
                    $attachments = [[
                        'path' => $message->attachment_path,
                        'name' => $message->attachment_name,
                        'mime_type' => $message->attachment_mime_type,
                        'size' => $message->attachment_size,
                    ]];

                    DB::table('branch_messages')
                        ->where('id', $message->id)
                        ->update(['attachments' => json_encode($attachments)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            $table->dropColumn('attachments');
        });
    }
};
