<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_assistant_interactions', function (Blueprint $table): void {
            $table->boolean('helpful')->nullable()->after('user_agent');
            $table->timestamp('feedback_submitted_at')->nullable()->after('helpful');
            $table->index(['helpful', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('system_assistant_interactions', function (Blueprint $table): void {
            $table->dropIndex(['helpful', 'created_at']);
            $table->dropColumn(['helpful', 'feedback_submitted_at']);
        });
    }
};
