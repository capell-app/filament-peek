<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="capell-action"
>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="noindex, nofollow" />
        <title>{{ $action->name }}</title>
    </head>
    <body>
        <main>
            <h1>{{ $action->name }}</h1>

            @if (session('public_action_status'))
                <p>{{ session('public_action_status') }}</p>
            @endif

            <form
                method="post"
                action="{{ route('capell-public-actions.submit', ['action' => $action->key]) }}"
            >
                @csrf

                @foreach ($fields as $field)
                    <label for="public-action-{{ $field['key'] }}">
                        {{ $field['label'] }}
                    </label>
                    <input
                        id="public-action-{{ $field['key'] }}"
                        name="{{ $field['key'] }}"
                        type="{{ $field['type'] }}"
                        value="{{ old($field['key']) }}"
                        @required($field['required'])
                    />
                    @error($field['key'])
                        <p>{{ $message }}</p>
                    @enderror
                @endforeach

                <button type="submit">
                    {{ __('capell-public-actions::generic.submit') }}
                </button>
            </form>
        </main>
    </body>
</html>
