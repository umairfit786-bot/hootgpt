<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledgebase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'chatbot_knowledgebases';

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function embedding()
    {
        return $this->belongsTo(ChatbotEmbedding::class, 'embedding_id');
    }
}
