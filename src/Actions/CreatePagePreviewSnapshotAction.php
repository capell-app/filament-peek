<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Actions;

use Capell\Core\Models\Page;
use Capell\FilamentPeek\Bridges\PublishingStudio\PeekWorkspaceBridge;
use Capell\FilamentPeek\Concerns\ResolvesPreviewContext;
use Capell\FilamentPeek\Data\PagePreviewSnapshotData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * @method static array{snapshot: PagePreviewSnapshotData, url: string} run(Page $page, array<string, mixed> $formState)
 */
final class CreatePagePreviewSnapshotAction
{
    use AsFake;
    use AsObject;
    use ResolvesPreviewContext;

    /**
     * @param  array<string, mixed>  $formState
     * @return array{snapshot: PagePreviewSnapshotData, url: string}
     */
    public function handle(Page $page, array $formState): array
    {
        $user = $this->currentPreviewUser();

        abort_unless($user instanceof Model, 403);

        $token = Str::random(48);
        $snapshot = new PagePreviewSnapshotData(
            token: $token,
            userId: $this->previewUserIdentifier($user),
            pageId: $this->modelIntKey($page),
            formState: $formState,
            workspaceId: $this->currentWorkspaceId(),
            layoutBuilderState: resolve(StoreLayoutBuilderPreviewStateAction::class)->resolve($page, $user),
        );

        $payload = $snapshot->toArray();

        $this->assertPreviewPayloadWithinLimit($payload, 'page_preview_snapshot');

        Cache::store($this->previewCacheStore())->put(
            $this->snapshotCacheKey($token),
            $payload,
            now()->addMinutes($this->previewTtlMinutes()),
        );

        $this->indexSnapshotToken($snapshot->pageId, $token);

        return [
            'snapshot' => $snapshot,
            'url' => URL::temporarySignedRoute(
                'capell-filament-peek.preview',
                now()->addMinutes($this->previewTtlMinutes()),
                ['token' => $token],
            ),
        ];
    }

    /**
     * Remember which tokens exist for a page so a save can revoke them. The index
     * lives as long as the longest-lived token it lists.
     */
    private function indexSnapshotToken(int $pageId, string $token): void
    {
        $store = Cache::store($this->previewCacheStore());
        $key = $this->pageSnapshotIndexCacheKey($pageId);
        $tokens = $store->get($key);
        $tokens = is_array($tokens) ? array_values(array_filter($tokens, is_string(...))) : [];
        $tokens[] = $token;

        $store->put($key, $tokens, now()->addMinutes($this->previewTtlMinutes()));
    }

    private function currentWorkspaceId(): ?int
    {
        return PeekWorkspaceBridge::currentWorkspaceId();
    }
}
