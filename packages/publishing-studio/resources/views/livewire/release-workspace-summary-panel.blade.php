<div>
    @if ($visible)
        <section class="space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ __('capell-admin::workspace.release.summary_title') }}
                </h3>

                @if ($summary->itemCount === 0)
                    <p class="text-sm text-gray-500">
                        {{ __('capell-admin::workspace.release.empty_summary') }}
                    </p>
                @else
                    <ul
                        class="mt-2 divide-y divide-gray-200 dark:divide-white/10"
                    >
                        @foreach ($summary->items as $item)
                            <li class="py-2 text-sm">
                                <span
                                    class="font-medium text-gray-950 dark:text-white"
                                >
                                    {{ $item->label }}
                                </span>
                                <span class="text-gray-500">
                                    &middot; {{ $item->source }} &middot;
                                    {{ $item->changeType }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                    {{ __('capell-admin::workspace.release.readiness_title') }}
                </h3>

                <p class="text-sm text-gray-500">
                    {{ trans_choice('capell-admin::workspace.release.blocking_count', $readiness->blockingIssueCount, ['count' => $readiness->blockingIssueCount]) }}
                </p>

                @if ($readiness->blockingIssueCount > 0)
                    <ul class="text-danger-600 mt-2 list-disc pl-5 text-sm">
                        @foreach ($readiness->blockingIssues as $issue)
                            <li>{{ $issue }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif
</div>
