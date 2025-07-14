<?php

return [
    'tts_model' => 'facebook/fastspeech2-en-ljspeech',
    'api_url' => 'https://api-inference.huggingface.co/models/',
    'api_token' => env('HUGGINGFACE_API_TOKEN'),
];