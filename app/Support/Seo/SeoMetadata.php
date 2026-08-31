<?php

namespace App\Support\Seo;

readonly class SeoMetadata
{
    public function __construct(
        public string $title,
        public ?string $metaDescription,
        public bool $robotsIndex,
        public ?string $ogImageUrl,
        public ?string $canonicalUrl,
    ) {}
}
