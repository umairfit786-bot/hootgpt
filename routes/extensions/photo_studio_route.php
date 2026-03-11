<?php

use App\Http\Controllers\User\PhotoStudioController;
use App\Http\Controllers\Admin\Extensions\AIPhotoStudioController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(AIPhotoStudioController::class)->group(function() {
        Route::get('/davinci/configs/photo-studio', 'index')->name('admin.davinci.configs.photo.studio');
        Route::post('/davinci/configs/photo-studio', 'store')->name('admin.davinci.configs.photo.studio.store');
        Route::get('/davinci/configs/photo-studio/credits', 'showCredits')->name('admin.davinci.configs.photo.studio.credits');
        Route::post('/davinci/configs/photo-studio/credits/store', 'storeCredits')->name('admin.davinci.configs.photo.studio.credits.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI PHOTO STUDIO ROUTES
    Route::controller(PhotoStudioController::class)->group(function () {
        Route::get('/photo-studio', 'index')->name('user.photo.studio');       
        Route::post('/photo-studio/generate', 'generate')->name('user.photo.studio.generate');                                
    });

});