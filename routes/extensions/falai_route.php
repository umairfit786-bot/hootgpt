<?php

use App\Http\Controllers\Admin\Extensions\FluxController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(FluxController::class)->group(function() {
        Route::get('/davinci/configs/flux', 'index')->name('admin.davinci.configs.flux');
        Route::post('/davinci/configs/flux', 'store')->name('admin.davinci.configs.flux.store');
    }); 
});
