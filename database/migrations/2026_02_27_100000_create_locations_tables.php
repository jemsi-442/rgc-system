<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code')->nullable()->unique();
                $table->boolean('is_zanzibar')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('regions', function (Blueprint $table) {
                if (! Schema::hasColumn('regions', 'is_zanzibar')) {
                    $table->boolean('is_zanzibar')->default(false)->after('code');
                }
            });
        }

        if (! Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->timestamps();

                $table->unique(['region_id', 'name']);
                $table->index('region_id');
            });
        } else {
            Schema::table('districts', function (Blueprint $table) {
                if (! Schema::hasColumn('districts', 'code')) {
                    $table->string('code')->nullable()->after('name');
                }
            });
        }

        if (! Schema::hasTable('churches')) {
            Schema::create('churches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
                $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
                $table->string('name');
                $table->enum('type', ['headquarters', 'regional', 'district', 'local'])->default('local');
                $table->string('slug')->nullable()->unique();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->foreignId('pastor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->softDeletes();

                $table->index('region_id');
                $table->index('district_id');
            });
        } else {
            Schema::table('churches', function (Blueprint $table) {
                if (! Schema::hasColumn('churches', 'type')) {
                    $table->enum('type', ['headquarters', 'regional', 'district', 'local'])->default('local')->after('name');
                }

                if (! Schema::hasColumn('churches', 'slug')) {
                    $table->string('slug')->nullable()->after('type');
                }

                if (! Schema::hasColumn('churches', 'address')) {
                    $table->string('address')->nullable()->after('slug');
                }

                if (! Schema::hasColumn('churches', 'phone')) {
                    $table->string('phone')->nullable()->after('address');
                }

                if (! Schema::hasColumn('churches', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }

                if (! Schema::hasColumn('churches', 'pastor_id')) {
                    $table->foreignId('pastor_id')->nullable()->after('email')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('churches', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('pastor_id');
                }

                if (! Schema::hasColumn('churches', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('churches')) {
            Schema::dropIfExists('churches');
        }

        if (Schema::hasTable('districts')) {
            Schema::dropIfExists('districts');
        }

        if (Schema::hasTable('regions')) {
            Schema::dropIfExists('regions');
        }
    }
};
