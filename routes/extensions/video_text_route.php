<?php

use App\Http\Controllers\User\VideoTextController;
use App\Http\Controllers\Admin\Extensions\VideoTextSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(VideoTextSettingController::class)->group(function() {
        Route::get('/davinci/configs/video-text', 'index')->name('admin.davinci.configs.video.text');
        Route::post('/davinci/configs/video-text', 'store')->name('admin.davinci.configs.video.text.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI VIDEO ROUTES
    Route::controller(VideoTextController::class)->group(function () {
        Route::get('/video/text', 'index')->name('user.video.text');       
        Route::post('/video/text/create', 'create')->name('user.video.text.create');         
        Route::post('/video/text/delete', 'delete');                                                   
    });

});