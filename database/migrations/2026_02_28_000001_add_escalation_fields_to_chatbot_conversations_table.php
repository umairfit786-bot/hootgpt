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
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->string('escalation_status')->nullable()->default(null);
            $table->timestamp('escalated_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->bigInteger('agent_id')->unsigned()->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn([
                'escalation_status',
                'escalated_at',
                'resolved_at',
                'agent_id',
                'ai_summary',
                'visitor_name',
                'visitor_country',
            ]);
        });
    }
};
