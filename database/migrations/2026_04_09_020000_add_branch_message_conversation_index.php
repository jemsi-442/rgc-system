<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            $table->index(
                ['church_id', 'created_at', 'id'],
                'branch_messages_church_id_created_at_id_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('branch_messages', function (Blueprint $table): void {
            $table->dropIndex('branch_messages_church_id_created_at_id_index');
        });
    }
};
