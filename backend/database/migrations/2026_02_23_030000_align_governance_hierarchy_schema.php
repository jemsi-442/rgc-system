<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table): void {
            $table->unique(['region_id', 'name'], 'districts_region_id_name_unique');
            $table->unique(['region_id', 'id'], 'districts_region_id_id_unique');
        });

        Schema::table('churches', function (Blueprint $table): void {
            if (!Schema::hasColumn('churches', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('id');
                $table->index('region_id');
            }

            if (!Schema::hasColumn('churches', 'type')) {
                $table->enum('type', ['headquarters', 'regional', 'district', 'local'])->default('local')->after('name');
            }
        });

        DB::statement('UPDATE churches c JOIN districts d ON d.id = c.district_id SET c.region_id = d.region_id WHERE c.region_id IS NULL');

        Schema::table('churches', function (Blueprint $table): void {
            $table->foreign('region_id')->references('id')->on('regions')->restrictOnDelete();
            $table->foreign(['region_id', 'district_id'], 'churches_region_district_fk')
                ->references(['region_id', 'id'])
                ->on('districts')
                ->restrictOnDelete();
        });

        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('role');
                $table->index('region_id');
            }

            if (!Schema::hasColumn('users', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('region_id');
                $table->index('district_id');
            }

            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('district_id');
                $table->index('branch_id');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });

        DB::statement('UPDATE users SET branch_id = church_id WHERE branch_id IS NULL AND church_id IS NOT NULL');
        DB::statement('UPDATE users u JOIN churches c ON c.id = u.branch_id SET u.district_id = c.district_id WHERE u.district_id IS NULL AND c.district_id IS NOT NULL');
        DB::statement('UPDATE users u JOIN churches c ON c.id = u.branch_id SET u.region_id = c.region_id WHERE u.region_id IS NULL AND c.region_id IS NOT NULL');

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('region_id')->references('id')->on('regions')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('churches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['region_id']);
            $table->dropIndex(['district_id']);
            $table->dropIndex(['branch_id']);
            $table->dropColumn(['region_id', 'district_id', 'branch_id', 'status', 'email_verified_at']);
        });

        Schema::table('churches', function (Blueprint $table): void {
            $table->dropForeign(['region_id']);
            $table->dropForeign('churches_region_district_fk');
            $table->dropIndex(['region_id']);
            $table->dropColumn(['region_id', 'type']);
        });

        Schema::table('districts', function (Blueprint $table): void {
            $table->dropUnique('districts_region_id_name_unique');
            $table->dropUnique('districts_region_id_id_unique');
        });
    }
};
