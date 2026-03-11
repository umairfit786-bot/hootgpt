<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_agent_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voice_agent_conversation_id');
            $table->enum('role', ['user', 'agent']);
            $table->text('content');
            $table->timestamps();

            $table->foreign('voice_agent_conversation_id')
                ->references('id')
                ->on('voice_agent_conversations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_agent_messages');
    }
};
