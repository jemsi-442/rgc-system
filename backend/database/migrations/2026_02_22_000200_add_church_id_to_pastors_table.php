<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastors', function (Blueprint $table) {
            if (!Schema::hasColumn('pastors', 'church_id')) {
                $table->unsignedBigInteger('church_id')->nullable()->index()->after('email');
            }
        });

        if (Schema::hasColumn('pastors', 'branch_id') && Schema::hasColumn('pastors', 'church_id')) {
            DB::table('pastors')
                ->whereNull('church_id')
                ->update(['church_id' => DB::raw('branch_id')]);
        }

        Schema::table('pastors', function (Blueprint $table) {
            $table->foreign('church_id')->references('id')->on('churches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pastors', function (Blueprint $table) {
            if (Schema::hasColumn('pastors', 'church_id')) {
                $table->dropForeign(['church_id']);
                $table->dropColumn('church_id');
            }
        });
    }
};
