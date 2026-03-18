<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (! Schema::hasColumn('events', 'region_id')) {
                    $table->foreignId('region_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
                }

                if (! Schema::hasColumn('events', 'district_id')) {
                    $table->foreignId('district_id')->nullable()->after('region_id')->constrained('districts')->nullOnDelete();
                }

                if (! Schema::hasColumn('events', 'church_id')) {
                    $table->foreignId('church_id')->nullable()->after('district_id')->constrained('churches')->nullOnDelete();
                }

                if (! Schema::hasColumn('events', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('church_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('events', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (! Schema::hasColumn('expenses', 'church_id')) {
                    $table->foreignId('church_id')->nullable()->after('id')->constrained('churches')->nullOnDelete();
                }

                if (! Schema::hasColumn('expenses', 'recorded_by')) {
                    $table->foreignId('recorded_by')->nullable()->after('date')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('expenses', 'receipt_path')) {
                    $table->string('receipt_path')->nullable()->after('recorded_by');
                }
            });
        }

        if (Schema::hasTable('offerings')) {
            Schema::table('offerings', function (Blueprint $table) {
                if (! Schema::hasColumn('offerings', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offerings') && Schema::hasColumn('offerings', 'deleted_at')) {
            Schema::table('offerings', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
