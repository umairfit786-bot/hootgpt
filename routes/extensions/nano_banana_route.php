<?php

use App\Http\Controllers\Admin\Extensions\NanoBananaController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(NanoBananaController::class)->group(function() {
        Route::get('/davinci/configs/nano-banana', 'index')->name('admin.davinci.configs.nanobanana');
        Route::post('/davinci/configs/nano-banana', 'store')->name('admin.davinci.configs.nanobanana.store');
    }); 
});
