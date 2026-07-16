## What it does

Filament Peek lets you preview unsaved changes to a Page before you save it, so you can check how the page will look without publishing your edits.

## Do I need to do anything?

Usually no. When you edit a Page, use the **Changes** action in the header to open its preview. The action is available only when you can update that Page.

## Where it shows up

On a Page edit screen, in the **Changes** action in the header. It opens a modal with fullscreen, tablet, and mobile presets, so you can inspect the current unsaved form state at common viewport sizes.

## Good to know

- The preview is available for Pages you are allowed to edit; it is not a generic preview for every record type.
- The preview is private and temporary: it uses a signed URL and a cache snapshot tied to your admin user. It does not change what visitors see until you save and publish.
- Snapshots expire after 15 minutes by default. If a preview expires, close it and open a fresh one from the editor.
- An administrator can adjust `CAPELL_FILAMENT_PEEK_TTL_MINUTES`, `CAPELL_FILAMENT_PEEK_MAX_PAYLOAD_KB`, and `CAPELL_FILAMENT_PEEK_CACHE_STORE` when the default expiry, cache store, or 512 KB payload limit does not suit the application. A state that exceeds the limit must be saved before it can be previewed.
- The modal defaults to fullscreen. Integrators can change `CAPELL_FILAMENT_PEEK_INITIAL_DEVICE_PRESET` or the published `capell-filament-peek.preview.device_presets` configuration; Diagnostics checks the signed route, preview actions, upstream plugin, and cache store.
