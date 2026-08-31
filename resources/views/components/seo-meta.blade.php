@props(['metadata'])

@if ($metadata instanceof \App\Support\Seo\SeoMetadata)
    <title>{{ $metadata->title }}</title>

    @if ($metadata->metaDescription)
        <meta name="description" content="{{ $metadata->metaDescription }}">
    @endif

    @unless ($metadata->robotsIndex)
        <meta name="robots" content="noindex, follow">
    @endunless

    @if ($metadata->canonicalUrl)
        <link rel="canonical" href="{{ $metadata->canonicalUrl }}">
    @endif

    <meta property="og:title" content="{{ $metadata->title }}">

    @if ($metadata->metaDescription)
        <meta property="og:description" content="{{ $metadata->metaDescription }}">
    @endif

    @if ($metadata->ogImageUrl)
        <meta property="og:image" content="{{ $metadata->ogImageUrl }}">
    @endif
@endif
