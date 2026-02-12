<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('church_id');
            $table->date('date');

            $table->integer('men')->default(0);
            $table->integer('women')->default(0);
            $table->integer('youth')->default(0);
            $table->integer('children')->default(0);

            $table->integer('total')->default(0);

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('recorded_by')->nullable();

            $table->timestamps();

            $table->foreign('church_id')->references('id')->on('churches')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();

            // Optional indexes for performance
            $table->index(['church_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
