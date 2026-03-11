<?php

use App\Http\Controllers\Admin\Extensions\SpeechifyCloneController;
use App\Http\Controllers\User\SpeechifyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(SpeechifyCloneController::class)->group(function() {
        Route::get('/davinci/configs/speechify-clone', 'index')->name('admin.davinci.configs.speechify.clone');
        Route::post('/davinci/configs/speechify-clone', 'store')->name('admin.davinci.configs.speechify.clone.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI VOICEOVER VOICE CLONING ROUTES
    Route::controller(SpeechifyController::class)->group(function() {
        Route::get('/voice-clone', 'index')->name('user.speechify.clone');
        Route::post('/voice-clone/synthesize','synthesize')->name('user.speechify.synthesize');        
        Route::post('/voice-clone/listen','listen')->name('user.speechify.listen');        
        Route::post('/voice-clone/create', 'create')->name('user.speechify.clone.create');
        Route::get('/voice-clone/status/{voiceId}', 'status')->name('user.speechify.clone.status');
        Route::get('/voice-clone/list', 'list')->name('user.speechify.clone.list');
        Route::post('/voice-clone/delete', 'delete')->name('user.speechify.clone.delete');        
    });
});


