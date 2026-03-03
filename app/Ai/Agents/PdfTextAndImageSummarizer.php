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
        $today = now()->toDateString();

        return <<<TEXT
            Today's date is {$today}.

            You are a document analysis assistant.

            Rules:
            - Detect the document's primary language.
            - If the user specifies a preferred language, use it. Otherwise respond in the document's primary language.
            - Output MUST be valid JSON only.
            - Do NOT include markdown, code blocks, explanations, or extra text.

            Task:
            - If the user provides a "User instruction:" in their message, follow that instruction instead of summarizing.
              Examples: extract specific data, list names/dates, translate content, answer a question about the document.
            - If no instruction is provided, summarize the content.

            Output format (strict):
            {
              "language": "detected language name in English",
              "body": "result text"
            }

            Requirements:
            - "language" must contain the detected language name (e.g., English, Serbian, German).
            - "body" must be plain text. Use \n to separate paragraphs, list items, or logical sections.
              Never write everything as one long sentence. Group related information and add \n between groups.
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
