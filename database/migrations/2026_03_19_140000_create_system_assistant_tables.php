<?php

use App\Support\SystemAssistantKnowledge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_assistant_topics', function (Blueprint $table): void {
            $table->id();
            $table->string('slug');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('answer');
            $table->json('keywords');
            $table->json('suggestions')->nullable();
            $table->json('roles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['slug', 'locale']);
        });

        Schema::create('system_assistant_interactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('locale', 5);
            $table->text('question');
            $table->text('normalized_question');
            $table->foreignId('matched_topic_id')->nullable()->constrained('system_assistant_topics')->nullOnDelete();
            $table->string('matched_slug')->nullable();
            $table->string('source', 40)->default('fallback');
            $table->unsignedInteger('confidence')->default(0);
            $table->text('answer');
            $table->json('role_snapshot')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
            $table->index(['matched_slug', 'created_at']);
        });

        $now = now();
        $rows = array_map(function (array $row) use ($now): array {
            return [
                'slug' => $row['slug'],
                'locale' => $row['locale'],
                'title' => $row['title'],
                'answer' => $row['answer'],
                'keywords' => json_encode($row['keywords'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suggestions' => json_encode($row['suggestions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'roles' => json_encode($row['roles'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => true,
                'is_system' => true,
                'sort_order' => $row['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, SystemAssistantKnowledge::defaultRows());

        DB::table('system_assistant_topics')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_assistant_interactions');
        Schema::dropIfExists('system_assistant_topics');
    }
};
