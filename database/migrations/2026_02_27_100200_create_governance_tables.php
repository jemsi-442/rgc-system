<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branch_messages')) {
            Schema::create('branch_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->timestamps();

                $table->index(['church_id', 'id']);
            });
        }

        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')->constrained('regions')->restrictOnDelete();
                $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
                $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->text('body');
                $table->timestamps();

                $table->index('region_id');
                $table->index('district_id');
                $table->index('church_id');
            });
        }

        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
                $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
                $table->foreignId('church_id')->nullable()->constrained('churches')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('event_date');
                $table->timestamps();

                $table->index('region_id');
                $table->index('district_id');
                $table->index('church_id');
            });
        }

        if (! Schema::hasTable('offerings')) {
            Schema::create('offerings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->string('recorded_by');
                $table->timestamps();
                $table->softDeletes();

                $table->index('church_id');
            });
        }

        if (! Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->string('description');
                $table->date('date');
                $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
                $table->string('receipt_path')->nullable();
                $table->timestamps();

                $table->index('church_id');
            });
        }

        if (! Schema::hasTable('slides')) {
            Schema::create('slides', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('image_path');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('sort_order');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('offerings');
        Schema::dropIfExists('events');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('branch_messages');
    }
};
