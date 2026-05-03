<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasColumn('users', 'api_token_expiry') &&
            !Schema::hasColumn('users', 'api_token_expires_at')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('api_token_expiry', 'api_token_expires_at');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn('users', 'api_token_expires_at') &&
            !Schema::hasColumn('users', 'api_token_expiry')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('api_token_expires_at', 'api_token_expiry');
            });
        }
    }
};
