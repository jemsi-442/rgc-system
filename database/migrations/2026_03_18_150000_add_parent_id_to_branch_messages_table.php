<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            if (! Schema::hasColumn('branch_messages', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('branch_messages')
                    ->nullOnDelete();
                $table->index(['church_id', 'parent_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            if (Schema::hasColumn('branch_messages', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
                $table->dropIndex('branch_messages_church_id_parent_id_index');
            }
        });
    }
};
