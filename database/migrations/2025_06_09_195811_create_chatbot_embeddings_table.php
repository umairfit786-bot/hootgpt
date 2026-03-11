<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatbot_embeddings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('chatbot_id');
            $table->string('engine');
            $table->string('type')->default('text');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->nullable();
            $table->longText('content')->nullable();
            $table->longText('embedding')->nullable();
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE chatbot_embeddings ADD INDEX idx_title_type_user (title(100), type(50), user_id)');
        DB::statement('ALTER TABLE chatbot_embeddings ADD INDEX idx_user_status (user_id, status(50))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatbot_embeddings');
    }
};
