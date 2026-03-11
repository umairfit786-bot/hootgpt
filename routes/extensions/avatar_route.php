<?php

use App\Http\Controllers\User\AvatarController;
use App\Http\Controllers\Admin\Extensions\AvatarSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['verified', '2fa.verify', 'role:admin', 'PreventBackHistory']], function() {
    Route::controller(AvatarSettingController::class)->group(function() {
        Route::get('/davinci/configs/avatar', 'index')->name('admin.davinci.configs.avatar');
        Route::post('/davinci/configs/avatar', 'store')->name('admin.davinci.configs.avatar.store');
    }); 
});

Route::group(['prefix' => 'user', 'middleware' => ['verified', '2fa.verify', 'role:user|admin|subscriber', 'subscription.check', 'PreventBackHistory']], function() {
    // USER AVATAR ROUTES
    Route::controller(AvatarController::class)->group(function () {          
        Route::get('/avatar', 'index')->name('user.extension.avatar');    
        Route::get('/avatar/results', 'results')->name('user.extension.avatar.results'); 
        Route::post('/avatar/result/delete', 'deleteResult'); 
        Route::get('/avatar/video', 'video')->name('user.extension.avatar.video.create');  
        Route::post('/avatar/video', 'generateVideo')->name('user.extension.avatar.video.create.store');    
        Route::get('/avatar/image', 'image')->name('user.extension.avatar.image.create');              
        Route::post('/avatar/image', 'generateImage')->name('user.extension.avatar.image.create.store');    
        Route::get('/avatar/list/image-avatars', 'listImageAvatars')->name('user.extension.avatar.list.images');    
        Route::get('/avatar/list/image-avatars/create', 'createImageAvatar')->name('user.extension.avatar.list.images.create');    
        Route::post('/avatar/list/image-avatars/create', 'uploadImageAvatar');    
        Route::post('/avatar/list/image-avatar/favorite', 'favoriteImageAvatars'); 
        Route::post('/avatar/list/video-avatar/favorite', 'favoriteVideoAvatars'); 
        Route::post('/avatar/list/voice/favorite', 'favoriteVoices'); 
        Route::get('/avatar/list/video-avatars', 'listVideoAvatars')->name('user.extension.avatar.list.videos');    
        Route::get('/avatar/list/video-avatars/view/{name}', 'showVideoAvatar')->name('user.extension.avatar.list.videos.view');
        Route::get('/avatar/list/voices', 'listVoices')->name('user.extension.avatar.voices');    
        Route::get('/avatar/list/uploads', 'uploads')->name('user.extension.avatar.uploads');                    
        Route::post('/avatar/list/uploads', 'processUpload');                    
    });

});