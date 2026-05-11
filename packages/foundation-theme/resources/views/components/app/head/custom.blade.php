<?php

use Capell\Core\Actions\ColorConverterAction;
use Capell\Frontend\Facades\Frontend;

$theme = Frontend::theme();
$site = Frontend::site();

$linkColor = ColorConverterAction::run($theme->getMeta('link_color'));
$linkColorActive = ColorConverterAction::run($theme->getMeta('link_color_active'));
$brandColor = ColorConverterAction::run($site->getMeta('brand_color'));

$dividerColor = $theme->getMeta('divider_color');
$dividerColorValue = is_string($dividerColor) && $dividerColor !== ''
    ? ColorConverterAction::run($dividerColor)
    : 'rgb(229, 231, 235)';

?>

@props([
    'title' => '',
    'keywords' => '',
    'description' => '',
])

<style>
    :root {
        --color-brand: {{ $brandColor }};
        --color-link: {{ $linkColor }};
        --color-link-active: {{ $linkColorActive }};
        --color-divider: {{ $dividerColorValue }};
    }
</style>

<script>
    ;(function () {
        function setupTheme() {
            const isDarkMode =
                localStorage.theme === 'dark' ||
                (!localStorage.theme &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)

            document.documentElement.classList.toggle('dark', isDarkMode)
        }

        function updateHeaderSticky() {
            document.body.classList.toggle('header-sticky', window.scrollY > 0)
        }

        function handleHeaderAndTheme() {
            setupTheme()

            const header = document.getElementById('header')
            if (!header) return
            updateHeaderSticky()
        }

        setupTheme()

        window.removeEventListener('scroll', updateHeaderSticky)
        window.addEventListener('scroll', updateHeaderSticky)

        document.addEventListener('livewire:load', updateHeaderSticky)
        document.addEventListener('livewire:navigated', handleHeaderAndTheme)
    })()
</script>
