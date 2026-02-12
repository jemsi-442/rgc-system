<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChurchesTable extends Migration
{
    public function up()
    {
        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->nullable()->index();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('pastor_id')->nullable()->index();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();

            // foreign keys (if districts/pastors tables exist)
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            // pastor_id will be nullable; if pastor created later, we can set relation manually.
        });
    }

    public function down()
    {
        Schema::dropIfExists('churches');
    }
}
