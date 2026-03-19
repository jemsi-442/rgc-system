<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_assistant_interactions', function (Blueprint $table): void {
            $table->text('feedback_note')->nullable()->after('helpful');
        });
    }

    public function down(): void
    {
        Schema::table('system_assistant_interactions', function (Blueprint $table): void {
            $table->dropColumn('feedback_note');
        });
    }
};
