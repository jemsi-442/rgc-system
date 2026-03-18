<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 25)->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('member')->after('status');
            }

            if (! Schema::hasColumn('users', 'region_id')) {
                $table->foreignId('region_id')->nullable()->after('role')->constrained('regions')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('region_id')->constrained('districts')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('district_id')->constrained('churches')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'church_id')) {
                $table->foreignId('church_id')->nullable()->after('branch_id')->constrained('churches')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'church_id')) {
                $table->dropConstrainedForeignId('church_id');
            }

            if (Schema::hasColumn('users', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }

            if (Schema::hasColumn('users', 'district_id')) {
                $table->dropConstrainedForeignId('district_id');
            }

            if (Schema::hasColumn('users', 'region_id')) {
                $table->dropConstrainedForeignId('region_id');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
