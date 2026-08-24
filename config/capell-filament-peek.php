<?php

declare(strict_types=1);

return [
    'enabled' => true,

    'preview' => [
        'cache_store' => env('CAPELL_FILAMENT_PEEK_CACHE_STORE'),
        'ttl_minutes' => (int) env('CAPELL_FILAMENT_PEEK_TTL_MINUTES', 15),
        'max_payload_kb' => (int) env('CAPELL_FILAMENT_PEEK_MAX_PAYLOAD_KB', 512),
        'route_prefix' => 'capell-filament-peek',
        'middleware' => ['web', 'signed'],

        /*
        |----------------------------------------------------------------
        | Upstream (pboivin/filament-peek) Device Presets
        |----------------------------------------------------------------
        |
        | FilamentPeekServiceProvider bridges these into the vendor
        | `filament-peek.devicePresets`/`initialDevicePreset` config keys
        | on boot. They only shape the vendor's own shared preview modal,
        | which Publishing Studio's WorkspacePeekPreviewAction still uses
        | directly via Peek::registerPreviewModal(). PeekPagePreviewAction
        | (the Page-edit "Preview changes" action) no longer depends on
        | this: it renders its own Capell-owned modal, configured below
        | under `modal_device_presets`. Keep this block's shape
        | (`icon`/`canRotatePreset`/`rotateIcon`) matching the vendor
        | package's own config/filament-peek.php, since that Blade
        | template reads it verbatim.
        |
        */

        'device_presets' => [
            'fullscreen' => [
                'icon' => 'heroicon-o-computer-desktop',
                'width' => '100%',
                'height' => '100%',
                'canRotatePreset' => false,
            ],
            'tablet' => [
                'icon' => 'heroicon-o-device-tablet',
                'rotateIcon' => true,
                'width' => '1024px',
                'height' => '768px',
                'canRotatePreset' => true,
            ],
            'mobile' => [
                'icon' => 'heroicon-o-device-phone-mobile',
                'width' => '390px',
                'height' => '844px',
                'canRotatePreset' => true,
            ],
        ],

        'initial_device_preset' => env('CAPELL_FILAMENT_PEEK_INITIAL_DEVICE_PRESET', 'fullscreen'),

        /*
        |----------------------------------------------------------------
        | Capell Modal Device Presets
        |----------------------------------------------------------------
        |
        | Used only by PeekPagePreviewAction's own Capell-owned preview
        | modal. It renders one labelled button per preset (translated
        | through capell-filament-peek::actions.preview.devices.{key})
        | plus a Rotate control for any preset with `rotatable` set to
        | true. `width`/`height` are the natural device dimensions; the
        | modal always fits them responsively inside the available
        | viewport instead of enforcing a fixed pixel box, so a mobile
        | preset stays legible on a short screen. Unrelated to, and safe
        | to change independently of, the upstream block above.
        |
        */

        'modal_device_presets' => [
            'desktop' => [
                'width' => null,
                'height' => null,
                'rotatable' => false,
            ],
            'tablet' => [
                'width' => '1024px',
                'height' => '768px',
                'rotatable' => true,
            ],
            'mobile' => [
                'width' => '390px',
                'height' => '844px',
                'rotatable' => true,
            ],
        ],

        'modal_initial_device_preset' => env('CAPELL_FILAMENT_PEEK_MODAL_INITIAL_DEVICE_PRESET', 'desktop'),

        'allow_iframe_overflow' => false,
        'allow_iframe_pointer_events' => false,
    ],
];
