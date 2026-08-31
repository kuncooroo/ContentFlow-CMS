@php
    $messages = collect([
        ['type' => 'success', 'text' => session(\App\Support\Ui\Flash::SUCCESS)],
        ['type' => 'success', 'text' => session('status')],
        ['type' => 'error', 'text' => session(\App\Support\Ui\Flash::ERROR)],
    ])->filter(fn (array $message): bool => filled($message['text']));
@endphp

@if ($messages->isNotEmpty())
    <div class="space-y-3" wire:ignore.self aria-live="polite">
        @foreach ($messages as $message)
            <div
                @class([
                    'flex items-start justify-between gap-3 rounded-md border px-4 py-3 text-sm shadow-sm',
                    'border-green-200 bg-green-50 text-green-800' => $message['type'] === 'success',
                    'border-red-200 bg-red-50 text-red-800' => $message['type'] === 'error',
                ])
                role="alert"
                x-data="{ visible: true }"
                x-show="visible"
                x-transition
            >
                <p>{{ $message['text'] }}</p>
                <button
                    type="button"
                    class="shrink-0 text-current opacity-70 hover:opacity-100"
                    x-on:click="visible = false"
                    aria-label="Dismiss"
                >
                    &times;
                </button>
            </div>
        @endforeach
    </div>
@endif
