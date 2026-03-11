<select id="model" name="model" class="form-select" onchange="updateModel()">   	
    @foreach ($models as $model)
        @if (trim($model) == 'gpt-3.5-turbo-0125')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 3.5 Turbo') }}</option>
        @elseif (trim($model) == 'gpt-4')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4') }}</option>
        @elseif (trim($model) == 'gpt-4o')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o') }}</option>
        @elseif (trim($model) == 'gpt-4o-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o mini') }}</option>
        @elseif (trim($model) == 'gpt-4o-search-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o Search Preview') }}</option>
        @elseif (trim($model) == 'gpt-4o-mini-search-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o mini Search Preview') }}</option>
        @elseif (trim($model) == 'gpt-4-0125-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4 Turbo') }}</option>
        @elseif (trim($model) == 'gpt-4.5-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.5') }}</option>
        @elseif (trim($model) == 'gpt-4.1')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1') }}</option>
        @elseif (trim($model) == 'gpt-4.1-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1 mini') }}</option>
        @elseif (trim($model) == 'gpt-4.1-nano')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1 nano') }}</option>
        @elseif (trim($model) == 'o1')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1') }}</option>
        @elseif (trim($model) == 'o1-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1 mini') }}</option>
        @elseif (trim($model) == 'o1-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1 pro') }}</option>
        @elseif (trim($model) == 'o3-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o3 mini') }}</option>
        @elseif (trim($model) == 'o3')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o3') }}</option>
        @elseif (trim($model) == 'o4-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o4 mini') }}</option>
        @elseif (trim($model) == 'gpt-5')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5') }}</option>
        @elseif (trim($model) == 'gpt-5-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5 mini') }}</option>
        @elseif (trim($model) == 'gpt-5-nano')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5 nano') }}</option>
        @elseif (trim($model) == 'gpt-5-chat-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5 chat') }}</option>
        @elseif (trim($model) == 'gpt-5.1')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5.1') }}</option>
        @elseif (trim($model) == 'gpt-5.2')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5.2') }}</option>
        @elseif (trim($model) == 'gpt-5.2-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 5.2 Pro') }}</option>
            @elseif (trim($model) == 'claude-sonnet-4-6')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.6 Sonnet') }}</option>
        @elseif (trim($model) == 'claude-sonnet-4-5')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.5 Sonnet') }}</option>
        @elseif (trim($model) == 'claude-haiku-4-5')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.5 Haiku') }}</option>
        @elseif (trim($model) == 'claude-opus-4-6')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.6 Opus') }}</option>
        @elseif (trim($model) == 'claude-opus-4-5-20251101')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.5 Opus') }}</option>
        @elseif (trim($model) == 'claude-opus-4-1-20250805')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4.1 Opus') }}</option>
        @elseif (trim($model) == 'claude-opus-4-20250514')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4 Opus') }}</option>
        @elseif (trim($model) == 'claude-sonnet-4-20250514')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 4 Sonnet') }}</option>
        @elseif (trim($model) == 'claude-3-opus-20240229')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 3 Opus') }}</option>
        @elseif (trim($model) == 'claude-3-7-sonnet-20250219')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 3.7 Sonnet') }}</option>
        @elseif (trim($model) == 'claude-3-5-sonnet-20241022')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 3.5v2 Sonnet') }}</option>
        @elseif (trim($model) == 'claude-3-5-haiku-20241022')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Anthropic | Claude 3.5 Haiku') }}</option>
        @elseif (trim($model) == 'gemini-2.5-flash-lite')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Google | Gemini 2.5 Flash Lite') }}</option>
        @elseif (trim($model) == 'gemini-2.5-flash')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Google | Gemini 2.5 Flash') }}</option>
        @elseif (trim($model) == 'gemini-2.5-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Google | Gemini 2.5 Pro') }}</option>
        @elseif (trim($model) == 'gemini-3-flash-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Google | Gemini 3 Flash') }}</option>
        @elseif (trim($model) == 'gemini-3-pro-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Google | Gemini 3 Pro') }}</option>
        @elseif (trim($model) == 'grok-4-1-fast-non-reasoning')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 4.1 Fast') }}</option>
        @elseif (trim($model) == 'grok-4-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 4') }}</option>
        @elseif (trim($model) == 'grok-3-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 3') }}</option>
        @elseif (trim($model) == 'grok-3-fast-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 3 Fast') }}</option>
        @elseif (trim($model) == 'grok-3-mini-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 3 Mini') }}</option>
        @elseif (trim($model) == 'grok-3-mini-fast-latest')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 3 Mini Fast') }}</option>
        @elseif (trim($model) == 'grok-2-1212')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 2') }}</option>
        @elseif (trim($model) == 'grok-2-vision-1212')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('xAI | Grok 2 Vision') }}</option>
        @elseif (trim($model) == 'deepseek-reasoner')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('DeepSeek R1') }}</option>
        @elseif (trim($model) == 'deepseek-chat')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('DeepSeek V3') }}</option>
        @elseif (trim($model) == 'sonar')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Perplexity | Sonar') }}</option>
        @elseif (trim($model) == 'sonar-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Perplexity | Sonar Pro') }}</option>
        @elseif (trim($model) == 'sonar-reasoning')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Perplexity | Sonar Reasoning') }}</option>
        @elseif (trim($model) == 'sonar-reasoning-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Perplexity | Sonar Reasoning Pro') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-micro-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova Micro') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-lite-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova Lite') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-pro-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova Pro') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-2-omni-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova 2 Omni') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-2-lite-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova 2 Lite') }}</option>
        @elseif (trim($model) == 'us.amazon.nova-2-pro-v1:0')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('Amazon | Nova 2 Pro') }}</option>
        @else
            @foreach ($fine_tunes as $fine_tune)
                @if (trim($model) == $fine_tune->model)
                    <option value="{{ $fine_tune->model }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | ') }} {{ $fine_tune->description }}</option>
                @endif
            @endforeach
        @endif
        
    @endforeach									
</select>