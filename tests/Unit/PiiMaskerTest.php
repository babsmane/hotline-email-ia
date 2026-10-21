<?php

namespace Tests\Unit;

use App\Services\PiiMasker;
use PHPUnit\Framework\TestCase;

class PiiMaskerTest extends TestCase
{
    public function test_masks_emails_and_phone_numbers(): void
    {
        $masked = (new PiiMasker())->mask('Écrivez à jean@example.com ou appelez le 77 453 16 56.');

        $this->assertStringContainsString('[EMAIL]', $masked);
        $this->assertStringContainsString('[TEL]', $masked);
        $this->assertStringNotContainsString('jean@example.com', $masked);
        $this->assertStringNotContainsString('453 16 56', $masked);
    }

    public function test_keeps_dates_and_short_numbers(): void
    {
        $masked = (new PiiMasker())->mask('Facture 2026-114 du 2026-10-21.');

        $this->assertStringContainsString('2026-114', $masked);
        $this->assertStringContainsString('2026-10-21', $masked);
    }
}
