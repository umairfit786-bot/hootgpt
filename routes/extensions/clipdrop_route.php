<?php

use App\Http\Controllers\Admin\Extensions\ClipdropController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(ClipdropController::class)->group(function() {
        Route::get('/davinci/configs/clipdrop', 'index')->name('admin.davinci.configs.clipdrop');
        Route::post('/davinci/configs/clipdrop', 'store')->name('admin.davinci.configs.clipdrop.store');
    }); 
});
