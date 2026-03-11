<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotEmbedding extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbot_embeddings';

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function chatbots()
    {
        return $this->belongsToMany(Chatbot::class, 'chatbot_knowledgebases', 'embedding_id', 'chatbot_id');
    }

    protected $casts = [
        'trained_at' => 'datetime',
    ];
}
