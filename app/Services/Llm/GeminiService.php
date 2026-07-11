<?php

namespace App\Services\Llm;

use App\Contracts\LlmServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements LlmServiceInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.llm.gemini.api_key');
        $this->model = config('services.llm.gemini.model', 'gemini-2.5-flash');
        $this->baseUrl = config('services.llm.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    }

    /**
     * Generate a contextual comment based on a post's content and desired tone.
     */
    public function generateComment(string $postContent, string $tone, ?string $customPrompt = null, ?\App\Models\Bot $bot = null): ?string
    {
        if (empty($this->apiKey)) {
            Log::error("[GeminiService] API Key is missing.");
            return null;
        }

        $systemInstruction = $this->buildSystemInstruction($tone, $customPrompt, $bot);
        
        $prompt = "Target Post Content:\n\"" . $postContent . "\"\n\nPlease generate exactly 1 short, natural, human-like comment for this post.";

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(30)->post($url, [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 150,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $commentText = trim($data['candidates'][0]['content']['parts'][0]['text']);
                    
                    // Remove quotes if the AI wraps the comment in quotes
                    $commentText = preg_replace('/^["\']|["\']$/', '', $commentText);
                    
                    return $commentText;
                }
            }

            Log::error("[GeminiService] API Error: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("[GeminiService] Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build the persona/instruction for the AI based on the tone and bot persona.
     */
    private function buildSystemInstruction(string $tone, ?string $customPrompt, ?\App\Models\Bot $bot): string
    {
        // 1. Check for complete override
        if ($bot && !empty($bot->system_prompt_override)) {
            return $bot->system_prompt_override;
        }

        // 2. Build Base Persona
        $base = "You are an everyday social media user leaving a natural, genuine comment on a post. Keep it very short (1-2 sentences max). Do not use hashtags. Do not sound like a bot or an AI. DO NOT use perfect punctuation (e.g. avoid ending with periods).";
        
        if ($bot) {
            $base .= "\nYour profile details:";
            if (!empty($bot->gender)) $base .= " Gender: {$bot->gender}.";
            if (!empty($bot->language)) $base .= " Language preferred: {$bot->language}.";
            
            if ($bot->slang_level === 'high') {
                $base .= " You use a lot of internet slang, abbreviations (like tbh, ngl, lol), and type casually.";
            } elseif ($bot->slang_level === 'none') {
                $base .= " You speak professionally and formally.";
            }

            if (!empty($bot->ai_persona)) {
                $base .= "\nSpecific Persona: {$bot->ai_persona}";
            }
        }

        // 3. Apply Tone/Goal
        if ($tone === 'custom' && !empty($customPrompt)) {
            return "{$base}\n\nYour specific goal for this comment is: {$customPrompt}";
        }

        $toneInstructions = [
            'positive' => "Your tone should be highly positive, encouraging, or appreciative. You may use 1 relevant emoji.",
            'negative' => "Your tone should be negative, disagreeing, or critical, but keep it polite. Do not use emojis.",
            'neutral'  => "Your tone should be completely neutral, acknowledging the post without strong emotion.",
        ];

        $toneInstruction = $toneInstructions[$tone] ?? $toneInstructions['neutral'];

        return "{$base}\n\n{$toneInstruction}";
    }
}
