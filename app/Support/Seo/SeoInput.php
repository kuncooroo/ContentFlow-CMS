<?php

namespace App\Support\Seo;

use Illuminate\Validation\Rule;

class SeoInput
{
    /**
     * @return array<string, list<mixed>>
     */
    public static function rules(): array
    {
        return [
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'string', 'max:2048', self::canonicalUrlRule()],
            'robots_index' => ['boolean'],
            'og_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{
     *     seo_title: ?string,
     *     meta_description: ?string,
     *     canonical_url: ?string,
     *     robots_index: bool,
     *     og_media_id: ?int
     * }
     */
    public static function normalize(array $validated): array
    {
        $canonicalUrl = isset($validated['canonical_url']) ? trim((string) $validated['canonical_url']) : null;

        if ($canonicalUrl === '') {
            $canonicalUrl = null;
        }

        return [
            'seo_title' => filled($validated['seo_title'] ?? null) ? trim((string) $validated['seo_title']) : null,
            'meta_description' => filled($validated['meta_description'] ?? null) ? trim((string) $validated['meta_description']) : null,
            'canonical_url' => $canonicalUrl,
            'robots_index' => (bool) ($validated['robots_index'] ?? true),
            'og_media_id' => $validated['og_media_id'] ?? null,
        ];
    }

    public static function canonicalUrlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || trim((string) $value) === '') {
                return;
            }

            if (! self::isValidCanonicalUrl(trim((string) $value))) {
                $fail('Enter a valid URL or site path starting with /.');
            }
        };
    }

    public static function isValidCanonicalUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return true;
        }

        return (bool) preg_match('#^/[a-zA-Z0-9\-_./?=&%]*$#', $url);
    }
}
