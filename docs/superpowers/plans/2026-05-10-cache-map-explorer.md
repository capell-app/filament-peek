# Cache Map Explorer Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the standalone cached model URLs page with a Site Health cache dependency explorer.

**Architecture:** Add small Action/Data query objects in `packages/html-cache` for overview and resource options. Keep the Livewire component as the interaction layer and reuse the existing cached URL table for the final URL drilldown.

**Tech Stack:** Laravel, Livewire, Filament tables, Pest, Spatie Laravel Data.

---

### Task 1: Remove Admin Resource Entry

**Files:**
- Modify: `packages/html-cache/src/Bridges/HtmlCacheAdminBridge.php`
- Modify: `packages/html-cache/tests/Feature/CachedModelUrlsTest.php`

- [ ] Stop registering `CachedModelUrlResource` in `HtmlCacheAdminBridge::register()`.
- [ ] Update tests to assert the bridge does not expose the resource while the model and permissions remain usable internally.

### Task 2: Add Cache Map Query Actions

**Files:**
- Create: `packages/html-cache/src/Data/CacheMap/CacheMapModelSummaryData.php`
- Create: `packages/html-cache/src/Data/CacheMap/CacheMapResourceSummaryData.php`
- Create: `packages/html-cache/src/Data/CacheMap/CacheMapOverviewData.php`
- Create: `packages/html-cache/src/Actions/BuildCacheMapOverviewAction.php`
- Create: `packages/html-cache/src/Actions/ListCacheMapResourceOptionsAction.php`
- Modify: `packages/html-cache/tests/Feature/CachedModelUrlsTest.php`

- [ ] Add data classes for model summaries, resource summaries, and overview totals.
- [ ] Add an overview action grouped by model and by resource, scoped through a provided base query.
- [ ] Add a resource option action that returns the top five resources for a model, with optional text search.
- [ ] Cover grouping, ranking, search, limit, and site scope in Pest.

### Task 3: Rework Site Health Cache Map UI

**Files:**
- Modify: `packages/html-cache/src/Livewire/SiteHealthCacheMap.php`
- Modify: `packages/html-cache/resources/views/livewire/site-health-cache-map.blade.php`
- Modify: `packages/html-cache/src/Filament/Resources/CachedModelUrls/Tables/CachedModelUrlsTable.php`
- Modify: `packages/html-cache/resources/lang/en/admin.php`
- Modify: `packages/html-cache/tests/Feature/CachedModelUrlsTest.php`

- [ ] Add Livewire properties for selected model, resource search, and selected resource key.
- [ ] Render overview tiles, model distribution bars, top resource impact list, and drilldown filters.
- [ ] Apply selected model/resource constraints to the URL table query.
- [ ] Keep clear/open URL table actions working.
- [ ] Verify with the package feature tests.
