<?php

use App\Http\Controllers\User\FaceswapController;
use App\Http\Controllers\Admin\Extensions\FaceswapSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(FaceswapSettingController::class)->group(function() {
        Route::get('/davinci/configs/faceswap', 'index')->name('admin.davinci.configs.faceswap');
        Route::post('/davinci/configs/faceswap', 'store')->name('admin.davinci.configs.faceswap.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI VIDEO ROUTES
    Route::controller(FaceswapController::class)->group(function () {
        Route::get('/faceswap', 'index')->name('user.extension.faceswap');       
        Route::post('/faceswap/create', 'create')->name('user.extension.faceswap.create');         
        Route::post('/faceswap/delete', 'delete');                                                   
    });

});