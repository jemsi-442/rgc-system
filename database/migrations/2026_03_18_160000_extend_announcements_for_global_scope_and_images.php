<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            if (! Schema::hasColumn('announcements', 'is_global')) {
                $table->boolean('is_global')->default(false)->after('created_by');
            }

            if (! Schema::hasColumn('announcements', 'image_path')) {
                $table->string('image_path')->nullable()->after('body');
            }

            if (! Schema::hasColumn('announcements', 'image_name')) {
                $table->string('image_name')->nullable()->after('image_path');
            }

            if (! Schema::hasColumn('announcements', 'image_mime_type')) {
                $table->string('image_mime_type')->nullable()->after('image_name');
            }
        });

        Schema::table('announcements', function (Blueprint $table): void {
            $table->foreignId('region_id')->nullable()->change();
            $table->foreignId('district_id')->nullable()->change();
            $table->foreignId('church_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->foreignId('region_id')->nullable(false)->change();
            $table->foreignId('district_id')->nullable(false)->change();
            $table->foreignId('church_id')->nullable(false)->change();

            if (Schema::hasColumn('announcements', 'image_mime_type')) {
                $table->dropColumn('image_mime_type');
            }

            if (Schema::hasColumn('announcements', 'image_name')) {
                $table->dropColumn('image_name');
            }

            if (Schema::hasColumn('announcements', 'image_path')) {
                $table->dropColumn('image_path');
            }

            if (Schema::hasColumn('announcements', 'is_global')) {
                $table->dropColumn('is_global');
            }
        });
    }
};
