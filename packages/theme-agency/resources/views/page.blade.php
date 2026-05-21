@once
    <style>
        .site-theme-shell {
            background: #09090b;
            color: #f8fafc;
            font-family: var(--theme-body-font, Inter, system-ui, sans-serif);
        }

        .site-theme-shell section {
            padding: clamp(4rem, 8vw, 8rem) 1.5rem;
        }

        .site-theme-shell section > div {
            max-width: 76rem;
            margin-inline: auto;
        }

        .site-theme-shell h1,
        .site-theme-shell h2 {
            max-width: 12ch;
            color: #fff;
            font-family: var(--theme-heading-font, inherit);
            font-size: clamp(3rem, 8vw, 6.5rem);
            font-weight: 900;
            line-height: 0.92;
            letter-spacing: 0;
        }

        .site-theme-shell p {
            max-width: 42rem;
            color: rgb(255 255 255 / 72%);
            font-size: 1.125rem;
            line-height: 1.8;
        }

        .site-theme-shell a {
            color: inherit;
        }

        .site-theme-skip-link {
            background: #ffffff;
            color: #09090b;
            font-weight: 800;
            left: 1rem;
            padding: 0.75rem 1rem;
            position: fixed;
            top: 1rem;
            transform: translateY(-150%);
            z-index: 50;
        }

        .site-theme-skip-link:focus {
            transform: translateY(0);
        }

        .site-theme-shell img,
        .site-theme-shell section div:empty {
            border-radius: 1.5rem;
        }
    </style>
@endonce

<a href="#main-content" class="site-theme-skip-link">
    {{ __('capell-theme-agency::generic.skip_to_content') }}
</a>

<div
    style="{{ collect($brand->tokens())->map(fn ($value, $token) => $token . ':' . $value)->implode(';') }}"
    class="site-theme-shell min-h-screen bg-zinc-950 text-zinc-950 antialiased"
>
    {!! $content !!}
</div>
