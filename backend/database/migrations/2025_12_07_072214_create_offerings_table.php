<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('church_id');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->string('recorded_by');
            $table->timestamps();

            $table->foreign('church_id')->references('id')->on('churches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
