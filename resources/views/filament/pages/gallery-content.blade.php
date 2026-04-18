<x-filament-panels::page>
    @php($selectedMediaSource = $this->getSelectedMediaSource())

    <div class="space-y-6">
        <x-filament::section>
            <div class="grid gap-6 xl:grid-cols-[minmax(18rem,24rem)_1fr]">
                <div class="space-y-3">
                    {{ $this->sourceForm }}

                    @if ($selectedMediaSource !== null)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm dark:border-white/10 dark:bg-white/5">
                            <div class="font-medium text-gray-950 dark:text-white">
                                {{ $selectedMediaSource->name }}
                            </div>

                            @if (filled($selectedMediaSource->description))
                                <div class="mt-2 text-gray-600 dark:text-gray-400">
                                    {{ $selectedMediaSource->description }}
                                </div>
                            @endif

                            <div class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <div>{{ __('filament-gallery::filament-gallery.gallery_page.disk') }}: {{ $selectedMediaSource->getDisk() }}</div>
                                <div>{{ __('filament-gallery::filament-gallery.gallery_page.directory') }}: {{ $selectedMediaSource->getDirectory() }}</div>
                                <div>{{ __('filament-gallery::filament-gallery.gallery_page.files_count') }}: {{ count($files) }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        <x-filament::button
                            color="gray"
                            wire:click="refreshGallery"
                            wire:loading.attr="disabled"
                        >
                            {{ __('filament-gallery::filament-gallery.gallery_page.refresh') }}
                        </x-filament::button>

                        <x-filament::button
                            color="danger"
                            wire:click="deleteSelectedFiles"
                            wire:loading.attr="disabled"
                        >
                            {{ __('filament-gallery::filament-gallery.gallery_page.delete_selected') }}
                        </x-filament::button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                        @forelse ($files as $file)
                            <label class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-primary-400 dark:border-white/10 dark:bg-white/5">
                                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-3 py-2 dark:border-white/10">
                                    <span class="truncate text-xs text-gray-500 dark:text-gray-400">
                                        {{ $file['name'] }}
                                    </span>

                                    <input
                                        type="checkbox"
                                        value="{{ $file['path'] }}"
                                        wire:model="selectedFiles"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >
                                </div>

                                <div class="aspect-square bg-gray-50 dark:bg-white/5">
                                    @if ($file['type'] === 'video')
                                        <video
                                            src="{{ $file['url'] }}"
                                            class="h-full w-full object-cover"
                                            controls
                                            preload="metadata"
                                        ></video>
                                    @elseif ($file['type'] === 'image')
                                        <img
                                            src="{{ $file['url'] }}"
                                            alt="{{ $file['name'] }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <div class="flex h-full items-center justify-center px-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('filament-gallery::filament-gallery.gallery_page.file_without_preview') }}
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 px-6 py-10 text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                                {{ __('filament-gallery::filament-gallery.gallery_page.empty') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                        <form wire:submit="saveUploads" class="space-y-4">
                            <div>
                                <div class="text-sm font-medium text-gray-950 dark:text-white">
                                    {{ __('filament-gallery::filament-gallery.gallery_page.upload_title') }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament-gallery::filament-gallery.gallery_page.upload_help') }}
                                </div>
                            </div>

                            {{ $this->form }}

                            @error('mediaSourceId')
                                <div class="text-sm text-danger-600">{{ $message }}</div>
                            @enderror

                            @error('uploads')
                                <div class="text-sm text-danger-600">{{ $message }}</div>
                            @enderror

                            @error('uploads.*')
                                <div class="text-sm text-danger-600">{{ $message }}</div>
                            @enderror

                            <div class="flex items-center gap-3">
                                <x-filament::button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('filament-gallery::filament-gallery.gallery_page.upload_submit') }}
                                </x-filament::button>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('filament-gallery::filament-gallery.gallery_page.upload_footer', ['size' => (int) ceil(config('filament-gallery.page.max_upload_size', 51200) / 1024)]) }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
