<div class="space-y-4">
    @forelse ($revisions as $revision)
        <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p
                        class="text-sm font-medium text-gray-950 dark:text-white"
                    >
                        {{ __('capell-publishing-studio::workspace.revisions.version_label', ['version' => $revision->version]) }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $revision->event_type->value }}
                    </p>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $revision->created_at?->diffForHumans() }}
                </p>
            </div>

            @if (filled($revision->notes))
                <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                    {{ $revision->notes }}
                </p>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('capell-publishing-studio::workspace.revisions.empty') }}
        </p>
    @endforelse
</div>
