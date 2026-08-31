@props(['content'])

<div {{ $attributes->merge(['class' => 'prose prose-slate max-w-none whitespace-pre-wrap text-slate-800']) }}>
    {{ $content }}
</div>
