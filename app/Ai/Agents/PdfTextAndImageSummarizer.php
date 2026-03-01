<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenAI)]
#[MaxTokens(100000)]

class PdfTextAndImageSummarizer implements Agent, Conversational, HasTools
{
    use Promptable;

    public function model(): string
    {
        return config('ai.providers.openai.model');
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<TEXT
            You are a document summarization assistant.

            Rules:
            - Detect the document's primary language.
            - If the user specifies a preferred language, use it. Otherwise respond in the document's primary language.
            - Output MUST be valid JSON only.
            - Do NOT include markdown, code blocks, explanations, or extra text.

            Task:
            - Summarize the provided content (text + any attached image/PDF).

            Output format (strict):
            {
              "language": "detected language name in English",
              "body": "summary text"
            }

            Requirements:
            - "language" must contain the detected language name (e.g., English, Serbian, German).
            - "body" must contain the summary as plain text with short paragraphs separated by line breaks.
            - No additional fields.
            - No surrounding text.
        TEXT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
