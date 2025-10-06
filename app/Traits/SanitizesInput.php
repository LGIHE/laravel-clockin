<?php

namespace App\Traits;

trait SanitizesInput
{
    /**
     * Sanitize the input data before validation.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = $this->sanitizeInput($this->all());
        $this->replace($sanitized);
    }

    /**
     * Recursively sanitize input data.
     */
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize a string value.
     */
    protected function sanitizeString(string $value): string
    {
        // Trim whitespace
        $value = trim($value);

        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Strip HTML tags (except for fields that explicitly allow HTML)
        if (!$this->allowsHtml($value)) {
            $value = strip_tags($value);
        }

        return $value;
    }

    /**
     * Determine if the field allows HTML content.
     * Override this method in specific form requests if needed.
     */
    protected function allowsHtml(string $value): bool
    {
        // By default, no HTML is allowed
        // Override in specific form requests for fields like 'description', 'message', etc.
        return false;
    }

    /**
     * Get fields that are allowed to contain HTML.
     * Override this in specific form requests.
     */
    protected function htmlAllowedFields(): array
    {
        return [];
    }
}
