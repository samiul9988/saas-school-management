<?php

namespace App\Services\Sms;

class SmsSegmentCalculator
{
    private const GSM7_BASIC_CHARS = '@$!"#%&\'()*+,-./0123456789:;<=>?ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
        ."\n\r_";

    private const GSM7_EXTRA_CHARS = ['£', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'Ç', 'Ø', 'ø', 'Å', 'å', 'Δ', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ', 'Ξ', 'Æ', 'æ', 'ß', 'É', '¤', 'Ä', 'Ö', 'Ñ', 'Ü', '§', '¿', 'ä', 'ö', 'ñ', 'ü', 'à'];

    public function calculate(string $message): int
    {
        $length = mb_strlen($message);

        if ($length === 0) {
            return 1;
        }

        if ($this->isGsm7Compatible($message)) {
            return $length <= 160 ? 1 : (int) ceil($length / 153);
        }

        return $length <= 70 ? 1 : (int) ceil($length / 67);
    }

    private function isGsm7Compatible(string $message): bool
    {
        foreach (mb_str_split($message) as $char) {
            if (! str_contains(self::GSM7_BASIC_CHARS, $char) && ! in_array($char, self::GSM7_EXTRA_CHARS, true)) {
                return false;
            }
        }

        return true;
    }
}
