<?php

use App\Http\Controllers\User\VoiceoverCloneController;
use App\Http\Controllers\Admin\Extensions\VoiceCloneController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(VoiceCloneController::class)->group(function() {
        Route::get('/davinci/configs/voice-clone', 'index')->name('admin.davinci.configs.voice.clone');
        Route::post('/davinci/configs/voice-clone', 'store')->name('admin.davinci.configs.voice.clone.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AI VOICEOVER VOICE CLONING ROUTES
    Route::controller(VoiceoverCloneController::class)->group(function() {
        Route::get('/text-to-speech/clone','index')->name('user.voiceover.clone'); 
        Route::post('/text-to-speech/clone/synthesize','synthesize')->name('user.voiceover.clone.synthesize');    
        Route::post('/text-to-speech/clone/listen','listen')->name('user.voiceover.clone.listen');         
        Route::post('/text-to-speech/clone/create','create')->name('user.voiceover.clone.create');         
        Route::post('/text-to-speech/clone/edit','edit');         
        Route::post('/text-to-speech/clone/audio','audio');           
        Route::post('/text-to-speech/clone/delete','delete');           
        Route::post('/text-to-speech/clone/voice/remove','voiceDelete');           
        Route::post('/text-to-speech/clone/configuration','configuration'); 
    });
});