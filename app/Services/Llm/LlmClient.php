<?php

namespace App\Services\Llm;

interface LlmClient
{
    /**
     * @return array{text: string, input_tokens: int, output_tokens: int}
     */
    public function complete(string $system, string $user): array;
}
