<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offerings') && ! Schema::hasColumn('offerings', 'description')) {
            Schema::table('offerings', function (Blueprint $table): void {
                $table->string('description')->nullable()->after('amount');
            });
        }

        if (! Schema::hasTable('offering_payments')) {
            Schema::create('offering_payments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('offering_id')->nullable()->constrained('offerings')->nullOnDelete();
                $table->string('public_reference')->unique();
                $table->string('provider')->default('snippe');
                $table->string('provider_reference')->unique();
                $table->string('status')->default('pending');
                $table->string('provider_status')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('currency', 3)->default('TZS');
                $table->date('offering_date')->nullable();
                $table->string('payer_name')->nullable();
                $table->string('payer_phone')->nullable();
                $table->string('payer_email')->nullable();
                $table->string('description')->nullable();
                $table->text('checkout_url')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->json('provider_payload')->nullable();
                $table->json('last_webhook_payload')->nullable();
                $table->timestamps();

                $table->index(['church_id', 'status']);
                $table->index(['user_id', 'created_at']);
                $table->index(['provider', 'provider_reference']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offering_payments');

        if (Schema::hasTable('offerings') && Schema::hasColumn('offerings', 'description')) {
            Schema::table('offerings', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }
};
