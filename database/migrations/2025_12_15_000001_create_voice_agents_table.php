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
        Schema::create('voice_agents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('uuid');
            $table->string('elevenlabs_agent_id')->nullable();
            $table->string('agent_name');
            $table->string('agent_title')->nullable();
            $table->text('system_prompt')->nullable();
            $table->string('first_message')->nullable();
            $table->string('voice_id')->nullable();
            $table->string('language')->default('en');
            $table->string('llm_model')->nullable();
            $table->string('avatar_image')->nullable();
            $table->string('accent_color')->default('#1e1e2d');
            $table->string('heading_color')->default('#ffffff');
            $table->string('agent_reply_bg_color')->default('#f5faff');
            $table->string('bg_color')->default('#ffffff');
            $table->string('text_color')->default('#1e1e2d');
            $table->string('widget_position')->default('right');
            $table->string('bubble_text')->default('Need help?');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voice_agents');
    }
};
