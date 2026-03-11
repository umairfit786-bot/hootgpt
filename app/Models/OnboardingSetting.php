<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingSetting extends Model
{
    protected $fillable = [
        'welcome_title',
        'welcome_message', 
        'welcome_icon',
        'welcome_banner',
        'completion_title',
        'completion_message',
        'completion_icon',
        'completion_banner'
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([]);
    }
}