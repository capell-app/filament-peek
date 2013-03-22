# Worked extension examples

These developer-facing recipes are kept beside the package contract. Replace the example values with the site-specific records and data objects used by the calling workflow.

<!-- example: contract Capell\FilamentPeek\Contracts\StoresLayoutBuilderPreviewState -->

```php
<?php
declare(strict_types=1);
final class ExampleStoresLayoutBuilderPreviewStateImplementation implements \Capell\FilamentPeek\Contracts\StoresLayoutBuilderPreviewState
{
    /**
     * @param  array<string, mixed>|null  $containers
     * @param  array<string, mixed>  $assets
     */
    public function handle(\Capell\Core\Contracts\Pageable $page, \Capell\Core\Models\Layout $layout, ?array $containers, array $assets = []): void
    {
        throw new LogicException('Implement this package contract for the calling site.');
    }
    public function clear(\Capell\Core\Contracts\Pageable $page, ?\Illuminate\Contracts\Auth\Authenticatable $user = null): void
    {
        throw new LogicException('Implement this package contract for the calling site.');
    }
}

app()->bind(\Capell\FilamentPeek\Contracts\StoresLayoutBuilderPreviewState::class, ExampleStoresLayoutBuilderPreviewStateImplementation::class);
```
