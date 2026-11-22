# Filament Peek

<!-- prettier-ignore-start -->

Press Preview changes on a Page's edit screen and a window opens over the editor, showing the page in your site's real theme with everything you have typed so far. Filament Peek adds that button, and using it saves nothing and publishes nothing.

By default the preview lasts 15 minutes, and only you can open it. A link sent to a colleague or client shows Preview unavailable. To share changes for review and sign-off, use Publishing Studio. It is a separate package that gives reviewers a preview link of their own and lets them approve changes before they are published. Preview changes covers Pages only, for people who may already edit that page.

Your new headline and swapped photo show up, and so does a moved block if you use the page builder. A banner across the top reads Unsaved preview, not published, and clicking inside the frame is limited. The preview will not match the live page pixel for pixel. Desktop, Tablet and Mobile buttons resize the window, and Rotate turns a tablet or phone sideways.

## What it does

Filament Peek lets you preview unsaved changes to a Page before you save it, so you can check how the page will look without publishing your edits.

## Do I need to do anything?

Usually no. When you edit a Page, use the **Preview changes** action in the header to open its preview. The action is available only when you can update that Page.

## Where it shows up

On a Page edit screen, in the **Preview changes** action in the header. It opens a modal that states the unsaved scope and how long the preview link stays valid, with labelled Desktop, Tablet, and Mobile buttons (plus Rotate where supported) so you can inspect the current unsaved form state at common viewport sizes. A mobile preview sizes itself to fit the modal rather than floating as a fixed-size box.

## Good to know

- The preview is available for Pages you are allowed to edit; it is not a generic preview for every record type.
- The preview is private and temporary: it uses a signed URL and a cache snapshot tied to your admin user. It does not change what visitors see until you save and publish.
- Snapshots expire after 15 minutes by default. If a preview expires or fails to render, the modal shows an "Open a fresh preview" recovery action rather than a dead iframe. Saving the page also expires every open preview of it, so reopen the preview to see the saved result.
- An administrator can adjust `CAPELL_FILAMENT_PEEK_TTL_MINUTES`, `CAPELL_FILAMENT_PEEK_MAX_PAYLOAD_KB`, and `CAPELL_FILAMENT_PEEK_CACHE_STORE` when the default expiry, cache store, or 512 KB payload limit does not suit the application. A state that exceeds the limit must be saved before it can be previewed.
- The Page-preview modal defaults to Desktop. Integrators can change `CAPELL_FILAMENT_PEEK_MODAL_INITIAL_DEVICE_PRESET` or the published `capell-filament-peek.preview.modal_device_presets` configuration; Diagnostics checks the signed route, preview actions, upstream Filament Peek package, and cache store. A separate `capell-filament-peek.preview.device_presets`/`CAPELL_FILAMENT_PEEK_INITIAL_DEVICE_PRESET` pair configures only the upstream shared modal that Publishing Studio's workspace preview still uses.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
