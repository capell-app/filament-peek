@props([
    'query' => '',
])

<form
    method="GET"
    action="{{ route('capell-frontend.search') }}"
    role="search"
    class="capell-site-search-form flex items-center gap-2"
>
    <label class="sr-only" for="capell-site-search-query">
        {{ __('capell-site-search::generic.search_label') }}
    </label>
    <input
        id="capell-site-search-query"
        type="search"
        name="q"
        value="{{ $query }}"
        placeholder="{{ __('capell-site-search::generic.search_placeholder') }}"
        class="h-9 min-w-0 rounded-md border border-gray-200 bg-white px-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
    />
    <button
        type="submit"
        class="inline-flex h-9 items-center rounded-md bg-primary px-3 text-sm font-medium text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-900"
    >
        {{ __('capell-site-search::button.search') }}
    </button>
</form>
