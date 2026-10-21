<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailClassificationTest extends TestCase
{
    use RefreshDatabase;

    private function fakeLlm(string $text): void
    {
        config(['services.anthropic.key' => 'test-key']);

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [['type' => 'text', 'text' => $text]],
                'usage'   => ['input_tokens' => 120, 'output_tokens' => 80],
            ]),
        ]);
    }

    private function validResult(array $override = []): string
    {
        return json_encode(array_merge([
            'category'        => 'incident_technique',
            'priority'        => 'haute',
            'sentiment'       => 'negatif',
            'language'        => 'fr',
            'summary'         => 'Le client ne peut plus se connecter.',
            'suggested_reply' => 'Bonjour, nous examinons votre problème de connexion.',
            'needs_human'     => false,
            'confidence'      => 0.93,
        ], $override));
    }

    private function payload(): array
    {
        return [
            'sender'  => 'client@example.com',
            'subject' => 'Connexion impossible',
            'body'    => 'Appelez-moi au 77 453 16 56, mon mail est client@example.com',
        ];
    }

    public function test_email_is_classified_and_pii_is_not_sent_to_the_model(): void
    {
        $this->fakeLlm($this->validResult());

        $this->postJson('/api/emails', $this->payload())
            ->assertCreated()
            ->assertJsonPath('status', 'classified')
            ->assertJsonPath('category', 'incident_technique')
            ->assertJsonPath('priority', 'haute');

        Http::assertSent(function ($request) {
            $content = $request->data()['messages'][0]['content'];

            return str_contains($content, '[EMAIL]')
                && str_contains($content, '[TEL]')
                && !str_contains($content, 'client@example.com')
                && !str_contains($content, '77 453 16 56');
        });
    }

    public function test_low_confidence_is_sent_to_a_human(): void
    {
        $this->fakeLlm($this->validResult(['confidence' => 0.4, 'needs_human' => false]));

        $this->postJson('/api/emails', $this->payload())
            ->assertCreated()
            ->assertJsonPath('needs_human', true);
    }

    public function test_invalid_model_output_marks_the_email_as_failed(): void
    {
        $this->fakeLlm('ceci n\'est pas du json');

        $this->postJson('/api/emails', $this->payload())
            ->assertCreated()
            ->assertJsonPath('status', 'failed')
            ->assertJsonPath('needs_human', true);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $this->fakeLlm($this->validResult(['category' => 'categorie_inventee']));

        $this->postJson('/api/emails', $this->payload())
            ->assertJsonPath('status', 'failed');
    }
}
