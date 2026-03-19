<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('system_assistant_topics', 'region_id')) {
            Schema::table('system_assistant_topics', function (Blueprint $table): void {
                $table->foreignId('region_id')
                    ->nullable()
                    ->after('locale')
                    ->constrained('regions')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('system_assistant_topic_versions', 'region_id')) {
            Schema::table('system_assistant_topic_versions', function (Blueprint $table): void {
                $table->foreignId('region_id')
                    ->nullable()
                    ->after('locale')
                    ->constrained('regions')
                    ->nullOnDelete();
            });
        }

        Schema::table('system_assistant_topics', function (Blueprint $table): void {
            $table->index(['region_id', 'locale', 'is_active'], 'assistant_topics_region_locale_active_idx');
        });

        Schema::table('system_assistant_topic_versions', function (Blueprint $table): void {
            $table->index(['region_id', 'action', 'created_at'], 'assistant_topic_versions_region_action_created_idx');
        });

        DB::table('system_assistant_topic_versions as versions')
            ->join('system_assistant_topics as topics', 'topics.id', '=', 'versions.topic_id')
            ->update(['versions.region_id' => DB::raw('topics.region_id')]);
    }

    public function down(): void
    {
        Schema::table('system_assistant_topic_versions', function (Blueprint $table): void {
            $table->dropIndex('assistant_topic_versions_region_action_created_idx');
        });

        Schema::table('system_assistant_topics', function (Blueprint $table): void {
            $table->dropIndex('assistant_topics_region_locale_active_idx');
        });

        if (Schema::hasColumn('system_assistant_topic_versions', 'region_id')) {
            Schema::table('system_assistant_topic_versions', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('region_id');
            });
        }

        if (Schema::hasColumn('system_assistant_topics', 'region_id')) {
            Schema::table('system_assistant_topics', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('region_id');
            });
        }
    }
};
