@once
    <style>
        .site-theme-shell {
            background: #f7f8f6;
            color: #0f172a;
            font-family: var(--theme-body-font, Inter, system-ui, sans-serif);
        }

        .site-theme-shell ::selection {
            background: color-mix(
                in srgb,
                var(--theme-accent, #f59e0b) 28%,
                transparent
            );
        }

        .site-theme-shell a {
            color: inherit;
        }

        .site-theme-shell h1,
        .site-theme-shell h2,
        .site-theme-shell h3 {
            font-family: var(--theme-heading-font, inherit);
            letter-spacing: 0;
        }

        .site-theme-shell img {
            background: #e5e7eb;
        }

        @media (prefers-color-scheme: dark) {
            .site-theme-shell {
                background: #020617;
                color: #f8fafc;
            }

            .site-theme-shell img {
                background: #111827;
            }
        }
    </style>
@endonce

<div
    style="{{ collect($brand->tokens())->map(fn ($value, $token) => $token . ':' . $value)->implode(';') }}"
    class="site-theme-shell min-h-screen bg-[#f7f8f6] text-slate-950 antialiased dark:bg-slate-950 dark:text-slate-50"
>
    {!! $content !!}
</div>
