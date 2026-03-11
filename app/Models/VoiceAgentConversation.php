<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceAgentConversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'voice_agent_conversations';

    public function voiceAgent(): BelongsTo
    {
        return $this->belongsTo(VoiceAgent::class, 'voice_agent_id');
    }

    public function messages()
    {
        return $this->hasMany(VoiceAgentMessage::class, 'voice_agent_conversation_id');
    }
}
