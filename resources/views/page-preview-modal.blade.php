@php
    // These are Capell's own modal_device_presets/modal_initial_device_preset
    // config keys — deliberately separate from device_presets/
    // initial_device_preset, which FilamentPeekServiceProvider bridges into
    // the upstream vendor modal that Publishing Studio's workspace preview
    // still depends on directly.
    $devicePresets = config('capell-filament-peek.preview.modal_device_presets', []);
    $devicePresets = is_array($devicePresets) ? $devicePresets : [];
    $devices = [];

    foreach ($devicePresets as $key => $preset) {
        if (! is_string($key) || ! is_array($preset)) {
            continue;
        }

        $devices[$key] = [
            'label' => __('capell-filament-peek::actions.preview.devices.' . $key),
            'width' => $preset['width'] ?? null,
            'height' => $preset['height'] ?? null,
            'rotatable' => (bool) ($preset['rotatable'] ?? false),
        ];
    }

    $initialDevice = config('capell-filament-peek.preview.modal_initial_device_preset', 'desktop');
    $initialDevice = is_string($initialDevice) && $initialDevice !== '' ? $initialDevice : 'desktop';
    $allowIframePointerEvents = (bool) config('capell-filament-peek.preview.allow_iframe_pointer_events', false);
@endphp

<div
    x-data="capellPagePreviewModal({
        devices: @js($devices),
        initialDevice: @js($initialDevice),
        allowIframePointerEvents: @js($allowIframePointerEvents),
        closeLabel: @js(__('capell-filament-peek::actions.preview.close')),
        loadingLabel: @js(__('capell-filament-peek::actions.preview.loading')),
        unavailableLabel: @js(__('capell-filament-peek::actions.preview.unavailable')),
        recoverLabel: @js(__('capell-filament-peek::actions.preview.recover')),
        rotateLabel: @js(__('capell-filament-peek::actions.preview.rotate')),
        deviceGroupLabel: @js(__('capell-filament-peek::actions.preview.device_group_label')),
    })"
    x-show="open"
    x-on:open-capell-page-preview-modal.window="handleOpen($event.detail)"
    x-on:keydown.escape.window="close()"
    x-cloak
    data-capell-page-preview-modal
    class="capell-peek-overlay"
    style="display: none"
>
    <div
        x-trap="open"
        x-on:click.self="close()"
        class="capell-peek-overlay-inner"
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="capell-peek-title"
            aria-describedby="capell-peek-scope"
            class="capell-peek-panel"
        >
            <header class="capell-peek-header">
                <div class="capell-peek-heading">
                    <h2
                        id="capell-peek-title"
                        class="capell-peek-title"
                        x-text="modalTitle"
                    ></h2>
                    <p
                        id="capell-peek-scope"
                        class="capell-peek-scope"
                    >
                        <span x-text="scopeLabel"></span>
                        <span x-show="subjectLabel"
                            >&nbsp;&mdash;&nbsp;<span
                                x-text="subjectLabel"
                            ></span
                        ></span>
                    </p>
                    <p
                        class="capell-peek-ttl"
                        x-text="ttlLabel"
                    ></p>
                </div>

                <div
                    class="capell-peek-devices"
                    role="group"
                    x-bind:aria-label="deviceGroupLabel"
                >
                    <template
                        x-for="device in deviceList"
                        x-bind:key="device.key"
                    >
                        <button
                            type="button"
                            class="capell-peek-device-button"
                            x-bind:class="{ 'is-active': activeDevice === device.key }"
                            x-bind:aria-pressed="(activeDevice === device.key).toString()"
                            x-bind:data-device-preset="device.key"
                            x-on:click="setDevice(device.key)"
                            x-text="device.label"
                        ></button>
                    </template>

                    <button
                        type="button"
                        class="capell-peek-rotate-button"
                        data-device-rotate
                        x-show="canRotate"
                        x-on:click="rotate()"
                        x-text="rotateLabel"
                    ></button>
                </div>

                <button
                    type="button"
                    class="capell-peek-close-button"
                    x-on:click="close()"
                    x-bind:aria-label="closeLabel"
                >
                    &times;
                </button>
            </header>

            <div
                class="capell-peek-body"
                x-bind:style="frameContainerStyle"
            >
                <div
                    class="capell-peek-status"
                    x-show="status !== 'ready'"
                    role="status"
                    aria-live="polite"
                >
                    <template x-if="status === 'loading'">
                        <p x-text="loadingLabel"></p>
                    </template>

                    <template x-if="status === 'error'">
                        <div class="capell-peek-recovery">
                            <p x-text="unavailableLabel"></p>
                            <button
                                type="button"
                                class="capell-peek-recover-button"
                                x-on:click="recover()"
                                x-text="recoverLabel"
                            ></button>
                        </div>
                    </template>
                </div>

                <iframe
                    x-ref="frame"
                    x-bind:src="iframeUrl"
                    x-bind:style="frameStyle"
                    x-on:load="onFrameLoad()"
                    class="capell-peek-frame"
                    title="Page preview"
                    frameborder="0"
                ></iframe>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        [data-capell-page-preview-modal] {
            position: fixed;
            inset: 0;
            z-index: 2147483000;
        }

        [data-capell-page-preview-modal] .capell-peek-overlay-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.7);
        }

        [data-capell-page-preview-modal] .capell-peek-panel {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 96rem;
            height: min(96vh, 100%);
            background: #ffffff;
            color: #0f172a;
            border-radius: 0.75rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        @media(prefers-color-scheme: dark)
        {
                   [data-capell-page-preview-modal] .capell-peek-panel {
                       background: #1e293b;
                       color: #f8fafc;
                   }
               }

               [data-capell-page-preview-modal] .capell-peek-header {
                   display: flex;
                   flex-wrap: wrap;
                   align-items: flex-start;
                   gap: 0.75rem;
                   padding: 1rem 1.25rem;
                   border-bottom: 1px solid rgba(148, 163, 184, 0.35);
               }

               [data-capell-page-preview-modal] .capell-peek-heading {
                   flex: 1 1 16rem;
                   min-width: 0;
               }

               [data-capell-page-preview-modal] .capell-peek-title {
                   margin: 0;
                   font-size: 1rem;
                   font-weight: 650;
                   line-height: 1.3;
                   white-space: nowrap;
                   overflow: hidden;
                   text-overflow: ellipsis;
               }

               [data-capell-page-preview-modal] .capell-peek-scope {
                   margin: 0.125rem 0 0;
                   font-size: 0.8125rem;
                   color: #64748b;
                   white-space: nowrap;
                   overflow: hidden;
                   text-overflow: ellipsis;
               }

               [data-capell-page-preview-modal] .capell-peek-ttl {
                   margin: 0.125rem 0 0;
                   font-size: 0.75rem;
                   color: #94a3b8;
               }

               [data-capell-page-preview-modal] .capell-peek-devices {
                   display: flex;
                   flex-wrap: wrap;
                   gap: 0.375rem;
               }

               [data-capell-page-preview-modal] .capell-peek-device-button,
               [data-capell-page-preview-modal] .capell-peek-rotate-button {
                   border: 1px solid rgba(148, 163, 184, 0.5);
                   background: transparent;
                   color: inherit;
                   border-radius: 0.375rem;
                   padding: 0.375rem 0.75rem;
                   font-size: 0.8125rem;
                   font-weight: 550;
                   cursor: pointer;
               }

               [data-capell-page-preview-modal] .capell-peek-device-button.is-active {
                   background: #0f172a;
                   color: #ffffff;
                   border-color: #0f172a;
               }
        @media(prefers-color-scheme: dark)
        {
                   [data-capell-page-preview-modal] .capell-peek-device-button.is-active {
                       background: #f8fafc;
                       color: #0f172a;
                       border-color: #f8fafc;
                   }
               }

               [data-capell-page-preview-modal] .capell-peek-close-button {
                   border: none;
                   background: transparent;
                   color: inherit;
                   font-size: 1.5rem;
                   line-height: 1;
                   cursor: pointer;
                   padding: 0.25rem 0.5rem;
               }

               [data-capell-page-preview-modal] .capell-peek-body {
                   position: relative;
                   flex: 1 1 auto;
                   display: flex;
                   align-items: center;
                   justify-content: center;
                   min-height: 0;
                   padding: 1rem;
                   background: #f1f5f9;
               }
        @media(prefers-color-scheme: dark)
        {
                   [data-capell-page-preview-modal] .capell-peek-body {
                       background: #0f172a;
                   }
               }

               [data-capell-page-preview-modal] .capell-peek-frame {
                   background: #ffffff;
                   border-radius: 0.5rem;
                   box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
               }

               [data-capell-page-preview-modal] .capell-peek-status {
                   position: absolute;
                   inset: 1rem;
                   display: flex;
                   align-items: center;
                   justify-content: center;
                   text-align: center;
                   background: rgba(241, 245, 249, 0.92);
                   border-radius: 0.5rem;
                   padding: 1rem;
               }
        @media(prefers-color-scheme: dark)
        {
                   [data-capell-page-preview-modal] .capell-peek-status {
                       background: rgba(15, 23, 42, 0.92);
                   }
               }

               [data-capell-page-preview-modal] .capell-peek-recovery {
                   display: flex;
                   flex-direction: column;
                   align-items: center;
                   gap: 0.75rem;
               }

               [data-capell-page-preview-modal] .capell-peek-recover-button {
                   border: 1px solid rgba(148, 163, 184, 0.5);
                   background: #0f172a;
                   color: #ffffff;
                   border-radius: 0.375rem;
                   padding: 0.5rem 1rem;
                   font-size: 0.8125rem;
                   font-weight: 600;
                   cursor: pointer;
               }
        @media(prefers-reduced-motion: reduce)
        {
                   [data-capell-page-preview-modal],
                   [data-capell-page-preview-modal] * {
                       transition: none !important;
                       animation: none !important;
                   }
               }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('capellPagePreviewModal', (config) => ({
                open: false,
                status: 'loading',
                modalTitle: '',
                scopeLabel: '',
                subjectLabel: '',
                ttlLabel: '',
                iframeUrl: null,
                activeDevice: config.initialDevice,
                rotated: {},
                livewireId: null,
                previouslyFocused: null,
                closeLabel: config.closeLabel,
                loadingLabel: config.loadingLabel,
                unavailableLabel: config.unavailableLabel,
                recoverLabel: config.recoverLabel,
                rotateLabel: config.rotateLabel,
                deviceGroupLabel: config.deviceGroupLabel,

                get deviceList() {
                    return Object.entries(config.devices || {}).map(([key, device]) => ({
                        key,
                        label: device.label,
                    }));
                },

                get activeDeviceConfig() {
                    return (config.devices || {})[this.activeDevice] || null;
                },

                get canRotate() {
                    return !!(this.activeDeviceConfig && this.activeDeviceConfig.rotatable);
                },

                get frameContainerStyle() {
                    return {};
                },

                get frameStyle() {
                    const device = this.activeDeviceConfig;
                    const style = {
                        border: 'none',
                        maxWidth: '100%',
                        maxHeight: '100%',
                    };

                    if (!device || !device.width || !device.height) {
                        style.width = '100%';
                        style.height = '100%';

                        return style;
                    }

                    const isRotated = !!this.rotated[this.activeDevice];
                    const width = isRotated ? device.height : device.width;
                    const height = isRotated ? device.width : device.height;

                    style.width = `min(${width}, 100%)`;
                    style.height = `min(${height}, 100%)`;
                    style.pointerEvents = config.allowIframePointerEvents ? 'auto' : 'auto';

                    return style;
                },

                handleOpen(detail) {
                    this.previouslyFocused = document.activeElement;
                    this.modalTitle = detail.modalTitle || '';
                    this.scopeLabel = detail.scopeLabel || '';
                    this.subjectLabel = detail.subjectLabel || '';
                    this.ttlLabel = detail.ttlLabel || '';
                    this.livewireId = detail.livewireId || null;
                    this.activeDevice = config.initialDevice;
                    this.rotated = {};
                    this.status = 'loading';
                    this.iframeUrl = detail.iframeUrl;
                    this.open = true;
                },

                setDevice(key) {
                    this.activeDevice = key;
                },

                rotate() {
                    this.rotated[this.activeDevice] = !this.rotated[this.activeDevice];
                },

                onFrameLoad() {
                    try {
                        const doc = this.$refs.frame.contentDocument;
                        const rendered = !!(doc && doc.querySelector('[data-capell-preview-ribbon]'));

                        this.status = rendered ? 'ready' : 'error';
                    } catch (error) {
                        this.status = 'error';
                    }
                },

                recover() {
                    this.status = 'loading';

                    if (this.livewireId && window.Livewire) {
                        const component = window.Livewire.find(this.livewireId);

                        if (component) {
                            component.call('mountAction', 'peekPagePreview');
                        }
                    }
                },

                close() {
                    if (!this.open) return;

                    this.open = false;
                    this.iframeUrl = null;
                    this.status = 'loading';

                    const target = this.previouslyFocused;

                    if (target && typeof target.focus === 'function') {
                        this.$nextTick(() => target.focus());
                    }
                },
            }));
        });
    </script>
@endonce
