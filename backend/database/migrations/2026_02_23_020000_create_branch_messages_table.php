<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index(['church_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_messages');
    }
};
