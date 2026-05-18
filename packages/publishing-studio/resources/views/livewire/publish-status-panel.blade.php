@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Icons\Heroicon;

    $state = $this->state();
@endphp

<div
    class="capell-publish-status-panel rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
    aria-labelledby="publish-status-panel-title"
>
    <div
        class="flex items-center gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-800"
    >
        @svg(Heroicon::OutlinedDocumentText->getIconForSize(IconSize::Small), 'h-4 w-4 text-gray-400', ['aria-hidden' => 'true'])
        <h3
            id="publish-status-panel-title"
            class="text-sm font-semibold text-gray-700 dark:text-gray-300"
        >
            {{ __('capell-admin::publish_panel.title') }}
        </h3>
    </div>

    <div class="space-y-3 px-4 py-3 text-sm">
        {{-- Status row --}}
        <div class="flex items-center justify-between gap-2">
            <span class="text-gray-500 dark:text-gray-400">
                {{ __('capell-admin::publish_panel.status') }}
            </span>
            <span class="font-medium text-gray-800 dark:text-gray-200">
                {{ $state->statusLabel() }}
            </span>
        </div>

        {{-- Workspace row (only shown when inside a workspace) --}}
        @if ($state->hasActiveContext())
            <div class="flex items-center justify-between gap-2">
                <span class="text-gray-500 dark:text-gray-400">
                    {{ __('capell-admin::publish_panel.workspace') }}
                </span>
                <span class="font-medium text-gray-800 dark:text-gray-200">
                    {{ $state->contextName }}
                    @if ($state->contextStatus !== null)
                        <span class="ml-1 text-xs text-gray-400">
                            ({{ $state->contextStatus }})
                        </span>
                    @endif
                </span>
            </div>
        @endif

        {{-- Last published row --}}
        @if ($state->publishedAt !== null)
            <div class="flex items-center justify-between gap-2">
                <span class="text-gray-500 dark:text-gray-400">
                    {{ __('capell-admin::publish_panel.last_published') }}
                </span>
                <span
                    class="font-medium text-gray-800 dark:text-gray-200"
                    title="{{ $state->publishedAt->toDateTimeString() }}"
                >
                    {{ $state->publishedAt->diffForHumans() }}
                </span>
            </div>
        @endif

        {{-- Preview URL --}}
        @if ($state->previewUrl !== null)
            <div class="pt-1">
                <a
                    class="text-primary-600 dark:text-primary-400 inline-flex items-center gap-1.5 text-xs hover:underline"
                    href="{{ $state->previewUrl }}"
                    rel="noopener"
                    target="_blank"
                >
                    @svg(Heroicon::OutlinedArrowTopRightOnSquare->getIconForSize(IconSize::Small), 'h-3.5 w-3.5', ['aria-hidden' => 'true'])
                    {{ __('capell-admin::publish_panel.preview') }}
                </a>
            </div>
        @endif
    </div>

    {{-- Extension slots injected by PublishPanelExtender implementations --}}
    @foreach ($this->extensions() as $extension)
        <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-800">
            {!! $extension !!}
        </div>
    @endforeach
</div>
