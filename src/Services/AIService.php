<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class AIService
{
    /**
     * @param string $jobName
     * @param string $exception
     * @param array $payload
     * @return string
     */
    public function analyzeFailure(string $jobName, string $exception, array $payload): string
    {
        if (!config('queue-monitor.ai.enabled')) {
            return "AI Analysis is disabled in configuration.";
        }

        $apiKey = config('queue-monitor.ai.api_key');
        if (empty($apiKey)) {
            return "AI API Key is not configured.";
        }

        $provider = config('queue-monitor.ai.provider', 'openai');

        try {
            if ($provider === 'openai') {
                return $this->callOpenAI($jobName, $exception, $payload);
            }

            if ($provider === 'anthropic') {
                return $this->callAnthropic($jobName, $exception, $payload);
            }

            return "Unsupported AI provider: $provider";
        } catch (Throwable $e) {
            Log::error("Queue Monitor AI Error: " . $e->getMessage());
            return "An error occurred while communicating with the AI service: " . $e->getMessage();
        }
    }

    /**
     * @param string $jobName
     * @param string $exception
     * @param array $payload
     * @return string
     * @throws JsonException
     */
    protected function callOpenAI(string $jobName, string $exception, array $payload): string
    {
        $model = config('queue-monitor.ai.model', 'gpt-4o-mini');

        $response = Http::withToken(config('queue-monitor.ai.api_key'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert Laravel developer. Analyze the following failed queue job and provide a concise explanation of why it failed and how to fix it. Use Markdown for formatting.'
                ],
                [
                    'role' => 'user',
                    'content' => "Job Name: $jobName\n\nException: $exception\n\nPayload: " . json_encode($payload, JSON_THROW_ON_ERROR)
                ]
            ],
            'temperature' => 0.2,
        ]);

        if ($response->failed()) {
            return "OpenAI API Error: " . $response->body();
        }

        return $response->json('choices.0.message.content') ?? 'No analysis received.';
    }

    /**
     * @param string $jobName
     * @param string $exception
     * @param array $payload
     * @return string
     * @throws JsonException
     */
    protected function callAnthropic(string $jobName, string $exception, array $payload): string
    {
        $model = config('queue-monitor.ai.model', 'claude-3-haiku-20240307');

        $response = Http::withHeaders([
            'x-api-key' => config('queue-monitor.ai.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "You are an expert Laravel developer. Analyze the following failed queue job and provide a concise explanation of why it failed and how to fix it. Use Markdown for formatting.\n\nJob Name: $jobName\n\nException: $exception\n\nPayload: " . json_encode($payload, JSON_THROW_ON_ERROR)
                ]
            ],
        ]);

        if ($response->failed()) {
            return "Anthropic API Error: " . $response->body();
        }

        return $response->json('content.0.text') ?? 'No analysis received.';
    }
}
