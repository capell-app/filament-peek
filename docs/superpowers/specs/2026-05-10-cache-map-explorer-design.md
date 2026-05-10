# Cache Map Explorer Design

## Goal

Replace the standalone cached model URLs admin page with a Site Health cache map explorer that starts with an overview, then lets admins drill into model resources and affected URLs.

## Design

The cached model URL index remains the backing data source, but it is no longer exposed as a first-class Filament resource page. Site Health owns the diagnostic workflow.

The cache map widget has three stages:

1. Overview: show total cached URLs, total dependency rows, model groups, and the most URL-impactful resources.
2. Resource drilldown: choose a model, then choose a resource from dynamically populated options. The initial resource list shows the top five resources for the selected model and site scope; searching narrows the same list.
3. URL detail: show the URLs containing the selected resource, with site, language, timestamps, open URL, and clear URL actions.

## Boundaries

- Query and grouping behavior belongs in focused Actions/Data under `packages/html-cache/src`.
- The Livewire component owns UI state only: selected model, resource search, selected resource.
- The existing clear cached URL action and table action permission checks remain the source of truth for destructive actions.
- Admin/editor cache-map details remain admin-only and site-scoped through the existing SiteScope behavior.

## Testing

Focused feature tests should prove:

- the cached model URL Filament resource is no longer registered by the admin bridge;
- overview groups records by model and ranks resources by URL count;
- resource options are scoped by model, search, site, and limited to five initial options;
- selecting a resource narrows the URL table to URLs containing that resource.
