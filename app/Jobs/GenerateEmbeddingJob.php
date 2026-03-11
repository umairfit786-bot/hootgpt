<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatbotEmbedding;
use App\Models\Chatbot;
use App\Http\Controllers\User\ExternalChatbotEmbeddingController;

class GenerateEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $embedding;
    protected $chatbot;
    protected $url;
    protected $type;

    public function __construct($param1, $param2 = null, $param3 = null)
    {
        if ($param1 instanceof ChatbotEmbedding) {
            $this->embedding = $param1;
        } else {
            $this->chatbot = $param1;
            $this->url = $param2;
            $this->type = $param3;
        }
    }

    public function handle()
    {
        $controller = new ExternalChatbotEmbeddingController();
        
        if ($this->embedding) {
            $controller->generateEmbedding($this->embedding);
        } elseif ($this->type === 'website') {
            $controller->crawlWebsiteBackground($this->chatbot, $this->url);
        }
    }
}