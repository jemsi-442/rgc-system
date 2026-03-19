<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_branch_targets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'church_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_branch_targets');
    }
};
