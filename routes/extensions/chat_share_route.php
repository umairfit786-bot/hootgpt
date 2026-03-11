<?php

use App\Http\Controllers\Admin\Extensions\ChatShareController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(ChatShareController::class)->group(function() {
        Route::get('/davinci/configs/chat-share', 'index')->name('admin.davinci.configs.chat.share');
        Route::post('/davinci/configs/chat-share', 'store')->name('admin.davinci.configs.chat.share.store');
    }); 
});
