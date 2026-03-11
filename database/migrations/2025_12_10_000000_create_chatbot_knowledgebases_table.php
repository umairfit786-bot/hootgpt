<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chatbot_knowledgebases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chatbot_id')->unsigned();
            $table->bigInteger('embedding_id')->unsigned();
            $table->timestamps();
            
            $table->index(['chatbot_id', 'embedding_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_knowledgebases');
    }
};
