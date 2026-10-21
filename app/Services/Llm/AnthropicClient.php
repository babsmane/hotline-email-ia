<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Http;

class AnthropicClient implements LlmClient
{
    public function complete(string $system, string $user): array
    {
        $response = Http::withHeaders([
                'x-api-key'         => (string) config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
            ])
            ->timeout(30)
            ->retry(2, 500)
            ->post(config('services.anthropic.url'), [
                'model'      => config('services.anthropic.model'),
                'max_tokens' => 800,
                'system'     => $system,
                'messages'   => [
                    ['role' => 'user', 'content' => $user],
                ],
            ])
            ->throw();

        $data = $response->json();

        return [
            'text'          => $data['content'][0]['text'] ?? '',
            'input_tokens'  => $data['usage']['input_tokens'] ?? 0,
            'output_tokens' => $data['usage']['output_tokens'] ?? 0,
        ];
    }
}
