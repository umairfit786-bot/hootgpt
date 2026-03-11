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
        Schema::create('onboarding_settings', function (Blueprint $table) {
            $table->id();
            $table->string('welcome_title')->default('Welcome to DaVinci AI!');
            $table->text('welcome_message')->nullable();
            $table->string('welcome_icon')->default('🎉');
            $table->string('welcome_banner')->nullable();
            $table->string('completion_title')->default('Tour Complete!');
            $table->text('completion_message')->nullable();
            $table->string('completion_icon')->default('🎊');
            $table->string('completion_banner')->nullable();
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
        Schema::dropIfExists('onboarding_settings');
    }
};
