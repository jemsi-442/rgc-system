<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('branch_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('message');
                $table->string('attachment_name')->nullable()->after('attachment_path');
                $table->string('attachment_mime_type')->nullable()->after('attachment_name');
                $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime_type');
            }
        });

        Schema::table('branch_messages', function (Blueprint $table) {
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('branch_messages')->whereNull('message')->update(['message' => '']);

        Schema::table('branch_messages', function (Blueprint $table) {
            if (Schema::hasColumn('branch_messages', 'attachment_size')) {
                $table->dropColumn([
                    'attachment_path',
                    'attachment_name',
                    'attachment_mime_type',
                    'attachment_size',
                ]);
            }
        });

        Schema::table('branch_messages', function (Blueprint $table) {
            $table->text('message')->nullable(false)->change();
        });
    }
};
