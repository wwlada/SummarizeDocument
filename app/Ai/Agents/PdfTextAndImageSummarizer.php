<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenAI)]
//#[UseCheapestModel]
#[Model('gpt-4o-mini')]
#[MaxTokens(100000)]

class PdfTextAndImageSummarizer implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<TEXT
            You are a document summarization assistant.

            Rules:
            - Detect the document's primary language.
            - Respond ONLY in that language if user didnt mention preferred language.

            Task:
            - Summarize the provided content (text + any attached image/PDF).

            Output:
            - Plain text only (no JSON, no code blocks, no markdown).
            - Use short paragraphs and line breaks for readability.
            - Be concise and accurate.
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
