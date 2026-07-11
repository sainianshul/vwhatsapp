<?php

namespace App\Contracts;

interface LlmServiceInterface
{
    /**
     * Generate a contextual comment based on a post's content and desired tone.
     *
     * @param string $postContent The content of the post to comment on.
     * @param string $tone The desired tone (e.g., positive, negative, neutral, custom).
     * @param string|null $customPrompt Any custom instructions for the LLM.
     * @param \App\Models\Bot|null $bot The bot that will post the comment, used for persona injection.
     * @return string|null The generated comment text, or null on failure.
     */
    public function generateComment(string $postContent, string $tone, ?string $customPrompt = null, ?\App\Models\Bot $bot = null): ?string;
}
