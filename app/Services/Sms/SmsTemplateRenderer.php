<?php

namespace App\Services\Sms;

class SmsTemplateRenderer
{
    /**
     * @param  array<string, scalar|null>  $values
     */
    public function render(string $content, array $values): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function (array $matches) use ($values) {
            return (string) ($values[$matches[1]] ?? '');
        }, $content);
    }
}
