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
        Schema::table('image_credits', function (Blueprint $table) {
            $table->integer('google_veo_31_video')->nullable()->default(1);
            $table->integer('google_veo_31_fast_video')->nullable()->default(1);
            $table->integer('google_veo_31_video_image')->nullable()->default(1);
            $table->integer('google_veo_31_fast_video_image')->nullable()->default(1);
            $table->integer('kling_3_pro_video')->nullable()->default(1);
            $table->integer('kling_o3_video')->nullable()->default(1);
            $table->integer('kling_3_pro_video_image')->nullable()->default(1);
            $table->integer('kling_o3_video_image')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_credits', function (Blueprint $table) {
            $table->dropColumn('google_veo_31_video');
            $table->dropColumn('google_veo_31_fast_video');
            $table->dropColumn('google_veo_31_video_image');
            $table->dropColumn('google_veo_31_fast_video_image');
            $table->dropColumn('kling_3_pro_video');
            $table->dropColumn('kling_o3_video');
            $table->dropColumn('kling_3_pro_video_image');
            $table->dropColumn('kling_o3_video_image');
        });
    }
};
