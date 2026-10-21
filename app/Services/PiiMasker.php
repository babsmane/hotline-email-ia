<?php

namespace App\Services;

class PiiMasker
{
    public function mask(string $text): string
    {
        // Adresses e-mail
        $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[EMAIL]', $text);

        // Numéros de téléphone ou longues séquences de chiffres (9 chiffres ou plus)
        $text = preg_replace_callback('/\+?\d[\d\s().-]{6,}\d/', function ($m) {
            return strlen(preg_replace('/\D/', '', $m[0])) >= 9 ? '[TEL]' : $m[0];
        }, $text);

        return $text;
    }
}
