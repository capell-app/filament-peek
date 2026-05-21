@once
    <style>
        .site-theme-shell {
            background:
                radial-gradient(
                    circle at 20% 0%,
                    color-mix(
                        in srgb,
                        var(--theme-primary, #6366f1) 18%,
                        transparent
                    ),
                    transparent 34rem
                ),
                #fff;
            color: #0f172a;
            font-family: var(--theme-body-font, Inter, system-ui, sans-serif);
        }

        .site-theme-shell section {
            padding: clamp(4rem, 7vw, 7rem) 1.5rem;
        }

        .site-theme-shell section > div {
            max-width: 74rem;
            margin-inline: auto;
        }

        .site-theme-shell h1,
        .site-theme-shell h2 {
            max-width: 14ch;
            color: #0f172a;
            font-family: var(--theme-heading-font, inherit);
            font-size: clamp(3rem, 7vw, 5.75rem);
            font-weight: 800;
            line-height: 0.98;
            letter-spacing: 0;
        }

        .site-theme-shell p {
            max-width: 42rem;
            color: #475569;
            font-size: 1.125rem;
            line-height: 1.8;
        }

        .site-theme-shell a {
            color: var(--theme-primary, #6366f1);
        }

        .site-theme-skip-link {
            background: #0f172a;
            color: #ffffff;
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
            border-radius: 1rem;
        }
    </style>
@endonce

<a href="#main-content" class="site-theme-skip-link">
    {{ __('capell-theme-saas::generic.skip_to_content') }}
</a>

<div
    style="{{ collect($brand->tokens())->map(fn ($value, $token) => $token . ':' . $value)->implode(';') }}"
    class="site-theme-shell min-h-screen bg-white text-slate-950 antialiased"
>
    {!! $content !!}
</div>
