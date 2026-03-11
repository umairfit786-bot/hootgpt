<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceAgentMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'voice_agent_messages';

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(VoiceAgentConversation::class, 'voice_agent_conversation_id');
    }
}
