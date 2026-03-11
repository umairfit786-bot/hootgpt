<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceAgent extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'voice_agents';

    public function conversations()
    {
        return $this->hasMany(VoiceAgentConversation::class, 'voice_agent_id');
    }

    public function embeddings()
    {
        return $this->hasMany(VoiceAgentEmbedding::class, 'voice_agent_id');
    }
}
