<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Actions;

use Capell\DocumentLifecycle\Enums\DocumentStatusEnum;
use Capell\DocumentLifecycle\Models\Document;
use Capell\DocumentLifecycle\Models\DocumentPublication;
use Capell\PublishingStudio\Models\PublishingRevision;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lorisleiva\Actions\Concerns\AsAction;

final class PublishDocumentAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>|string|Model  $content
     * @param  array<string, mixed>  $metadata
     */
    public function handle(
        Document $document,
        array|string|Model $content,
        ?PublishingRevision $publishedRevision = null,
        ?string $versionLabel = null,
        ?Model $publishedActor = null,
        ?CarbonInterface $publishedAt = null,
        array $metadata = [],
    ): DocumentPublication {
        $publishedAt ??= now();

        $publication = $document->publications()->updateOrCreate(
            [
                'version_label' => $versionLabel ?? $this->versionLabel($publishedRevision, $publishedAt),
            ],
            [
                'published_revision_id' => $publishedRevision?->getKey(),
                'content_hash' => ComputeDocumentContentHashAction::run($content),
                'published_actor_type' => $this->morphType($publishedActor),
                'published_actor_id' => $publishedActor?->getKey(),
                'published_at' => $publishedAt,
                'metadata' => $metadata === [] ? null : $metadata,
            ],
        );

        if ($document->status !== DocumentStatusEnum::Active) {
            $document->forceFill(['status' => DocumentStatusEnum::Active])->save();
        }

        return $publication;
    }

    private function versionLabel(?PublishingRevision $revision, CarbonInterface $publishedAt): string
    {
        if ($revision instanceof PublishingRevision) {
            return 'r' . $revision->version;
        }

        return $publishedAt->format('Y-m-d.His');
    }

    private function morphType(?Model $model): ?string
    {
        if (! $model instanceof Model) {
            return null;
        }

        $alias = array_search($model::class, Relation::morphMap(), true);

        return is_string($alias) ? $alias : $model::class;
    }
}
