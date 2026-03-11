<?php

use App\Http\Controllers\User\VideoImageController;
use App\Http\Controllers\Admin\Extensions\VideoImageSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(VideoImageSettingController::class)->group(function() {
        Route::get('/davinci/configs/video-image', 'index')->name('admin.davinci.configs.video.image');
        Route::post('/davinci/configs/video-image', 'store')->name('admin.davinci.configs.video.image.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI VIDEO ROUTES
    Route::controller(VideoImageController::class)->group(function () {
        Route::get('/video', 'index')->name('user.video');       
        Route::post('/video/create', 'create')->name('user.video.create');     
        Route::get('/video/{id}/show', 'show')->name('user.video.show');        
        Route::post('/video/delete', 'delete');                                                   
    });

});