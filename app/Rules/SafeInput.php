<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeInput implements ValidationRule
{
    /**
     * Rejects input containing SQL injection, XSS, path traversal or other malicious patterns.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $dangerousPatterns = [
            'union select', 'drop table', "' or '1'='1", '" or "1"="1',
            '<script', 'javascript:', 'vbscript:', 'onerror=', 'onload=', '<iframe',
            '../', '..\\', '%2e%2e%2f',
            "\x00", '<?php', '<?=', '${',
        ];

        $lower = strtolower($value);

        foreach ($dangerousPatterns as $pattern) {
            if (stripos($lower, strtolower($pattern)) !== false) {
                $fail("El campo {$attribute} contiene caracteres o patrones no permitidos por seguridad.");
                return;
            }
        }
    }
}
