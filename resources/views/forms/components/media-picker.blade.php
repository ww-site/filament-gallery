@php
    $fieldWrapperView = $getFieldWrapperView();
    $state = $getState();
    $previewUrl = $getPreviewUrl();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div class="space-y-4">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="h-[250px] bg-gray-50 dark:bg-white/5">
                @if (filled($previewUrl))
                    <img
                        src="{{ $previewUrl }}"
                        alt="{{ $state }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <div class="flex h-full items-center justify-center px-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('filament-gallery::filament-gallery.picker.preview_empty') }}
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-100 px-4 py-3 text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                @if (filled($state))
                    {{ $state }}
                @else
                    {{ __('filament-gallery::filament-gallery.picker.path_empty') }}
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            {{ $getAction('uploadImage') }}
            {{ $getAction('selectFromGallery') }}
            {{ $getAction('clearImage') }}
        </div>
    </div>
</x-dynamic-component>
