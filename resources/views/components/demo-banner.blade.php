@if (config('demo.enabled'))
    <div class="border-b border-amber-300 bg-amber-400 px-4 py-2 text-center text-sm font-medium text-amber-950">
        Demo mode — explore freely. Destructive baseline changes are disabled and data can be reset with
        <code class="rounded bg-amber-300/60 px-1">php artisan demo:reset</code>.
    </div>
@endif
