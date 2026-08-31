<div class="mx-auto max-w-4xl space-y-6">
<form wire:submit="save" class="space-y-6">
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">General</h2>
            <p class="mt-1 text-sm text-slate-600">Site identity and contact details shown on the public site.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-slate-700">Site name</label>
                    <input
                        id="site_name"
                        type="text"
                        wire:model="site_name"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                    >
                    @error('site_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="site_description" class="block text-sm font-medium text-slate-700">Site description</label>
                    <textarea
                        id="site_description"
                        wire:model="site_description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
                    ></textarea>
                    @error('site_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-slate-700">Contact email</label>
                        <input id="contact_email" type="email" wire:model="contact_email" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-slate-700">Contact phone</label>
                        <input id="contact_phone" type="text" wire:model="contact_phone" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_address" class="block text-sm font-medium text-slate-700">Contact address</label>
                    <textarea id="contact_address" wire:model="contact_address" rows="3" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                    @error('contact_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Branding</h2>
            <p class="mt-1 text-sm text-slate-600">Logo and favicon media used on the public site.</p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="logo_media_id" class="block text-sm font-medium text-slate-700">Logo</label>
                    <select id="logo_media_id" wire:model="logo_media_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">None</option>
                        @foreach ($mediaItems as $media)
                            <option value="{{ $media->id }}">{{ $media->original_name }}</option>
                        @endforeach
                    </select>
                    @error('logo_media_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="favicon_media_id" class="block text-sm font-medium text-slate-700">Favicon</label>
                    <select id="favicon_media_id" wire:model="favicon_media_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">None</option>
                        @foreach ($mediaItems as $media)
                            <option value="{{ $media->id }}">{{ $media->original_name }}</option>
                        @endforeach
                    </select>
                    @error('favicon_media_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">SEO defaults</h2>
            <p class="mt-1 text-sm text-slate-600">Used when posts and pages do not define their own SEO overrides.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="default_seo_title" class="block text-sm font-medium text-slate-700">Default SEO title</label>
                    <input id="default_seo_title" type="text" wire:model="default_seo_title" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    @error('default_seo_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="default_meta_description" class="block text-sm font-medium text-slate-700">Default meta description</label>
                    <textarea id="default_meta_description" wire:model="default_meta_description" rows="3" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                    @error('default_meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="default_og_media_id" class="block text-sm font-medium text-slate-700">Default Open Graph image</label>
                    <select id="default_og_media_id" wire:model="default_og_media_id" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">None</option>
                        @foreach ($mediaItems as $media)
                            <option value="{{ $media->id }}">{{ $media->original_name }}</option>
                        @endforeach
                    </select>
                    @error('default_og_media_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" wire:model="default_robots_index" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    Allow search engines to index content by default
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Social links</h2>
            <p class="mt-1 text-sm text-slate-600">Provider name and full URL for each social profile.</p>

            <div class="mt-4 space-y-3">
                @forelse ($socialLinkRows as $index => $row)
                    <div wire:key="social-link-{{ $index }}" class="grid gap-3 md:grid-cols-[1fr_2fr_auto]">
                        <input
                            type="text"
                            wire:model="socialLinkRows.{{ $index }}.provider"
                            placeholder="twitter"
                            class="rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >
                        <input
                            type="url"
                            wire:model="socialLinkRows.{{ $index }}.url"
                            placeholder="https://twitter.com/example"
                            class="rounded-md border border-slate-300 px-3 py-2 text-sm"
                        >
                        <button type="button" wire:click="removeSocialLink({{ $index }})" class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-700 hover:bg-red-50">
                            Remove
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No social links configured.</p>
                @endforelse

                @error('social_links')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('social_links.*')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button type="button" wire:click="addSocialLink" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Add social link
                </button>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Regional & content</h2>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="timezone" class="block text-sm font-medium text-slate-700">Timezone</label>
                    <select id="timezone" wire:model="timezone" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($timezones as $timezoneOption)
                            <option value="{{ $timezoneOption }}">{{ $timezoneOption }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="locale" class="block text-sm font-medium text-slate-700">Locale</label>
                    <input id="locale" type="text" wire:model="locale" placeholder="en" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    @error('locale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" wire:model="comments_enabled" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    Enable public comments
                </label>
                @error('comments_enabled')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Save settings
            </button>
        </div>
    </form>
</div>
