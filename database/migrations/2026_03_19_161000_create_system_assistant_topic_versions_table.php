<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_assistant_topic_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained('system_assistant_topics')->nullOnDelete();
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
            $table->string('action', 40)->default('updated');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('restored_from_version_id')->nullable()->constrained('system_assistant_topic_versions')->nullOnDelete();
            $table->timestamps();
            $table->index(['topic_id', 'created_at']);
            $table->index(['slug', 'locale', 'created_at']);
        });

        $now = now();

        $rows = DB::table('system_assistant_topics')
            ->select([
                'id as topic_id',
                'slug',
                'locale',
                'title',
                'answer',
                'keywords',
                'suggestions',
                'roles',
                'is_active',
                'is_system',
                'sort_order',
                'updated_by as created_by',
            ])
            ->orderBy('id')
            ->get()
            ->map(fn ($topic) => [
                'topic_id' => $topic->topic_id,
                'slug' => $topic->slug,
                'locale' => $topic->locale,
                'title' => $topic->title,
                'answer' => $topic->answer,
                'keywords' => $topic->keywords,
                'suggestions' => $topic->suggestions,
                'roles' => $topic->roles,
                'is_active' => $topic->is_active,
                'is_system' => $topic->is_system,
                'sort_order' => $topic->sort_order,
                'action' => 'baseline',
                'created_by' => $topic->created_by,
                'restored_from_version_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($rows !== []) {
            DB::table('system_assistant_topic_versions')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_assistant_topic_versions');
    }
};
