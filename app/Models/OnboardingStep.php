<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingStep extends Model
{
    protected $fillable = [
        'target',
        'title',
        'content',
        'icon',
        'banner',
        'position',
        'order',
        'name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function getActiveSteps()
    {
        return self::where('active', true)
            ->orderBy('order')
            ->get()
            ->toArray();
    }
}