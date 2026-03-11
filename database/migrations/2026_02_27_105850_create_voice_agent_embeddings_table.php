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
        Schema::create('voice_agent_embeddings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('voice_agent_id')->unsigned();
            $table->string('engine')->default('openai');
            $table->string('type')->default('text');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->nullable();
            $table->longText('content')->nullable();
            $table->longText('embedding')->nullable();
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('voice_agent_id')->references('id')->on('voice_agents')->onDelete('cascade');
            
            $table->index(['voice_agent_id', 'status'], 'idx_voice_agent_status');
            $table->index(['user_id', 'type'], 'idx_user_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voice_agent_embeddings');
    }
};
