<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('onboarding_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('target')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('icon')->nullable();
            $table->string('banner')->nullable();
            $table->enum('position', ['top', 'bottom', 'left', 'right'])->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onboarding_steps');
    }
};
