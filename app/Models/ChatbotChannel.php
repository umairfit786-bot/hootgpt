<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotChannel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbot_channels';

    protected $casts = [
        'connected_at' => 'datetime',
        'credentials' => 'array'
    ];
}
