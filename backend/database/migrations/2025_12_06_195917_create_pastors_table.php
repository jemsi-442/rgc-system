<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastorsTable extends Migration
{
    public function up()
    {
        Schema::create('pastors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index(); // branch -> church id
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('churches')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pastors');
    }
}
