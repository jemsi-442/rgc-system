<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offering_payments', function (Blueprint $table): void {
            $table->timestamp('admin_notified_at')->nullable()->after('receipt_emailed_at');
        });
    }

    public function down(): void
    {
        Schema::table('offering_payments', function (Blueprint $table): void {
            $table->dropColumn('admin_notified_at');
        });
    }
};