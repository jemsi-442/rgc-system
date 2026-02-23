<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('churches', function (Blueprint $table) {
            if (!Schema::hasColumn('churches', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('offerings', function (Blueprint $table) {
            if (!Schema::hasColumn('offerings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('churches', function (Blueprint $table) {
            if (Schema::hasColumn('churches', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('offerings', function (Blueprint $table) {
            if (Schema::hasColumn('offerings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
