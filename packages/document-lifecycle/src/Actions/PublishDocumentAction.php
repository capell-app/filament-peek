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
use LogicException;
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
        $versionLabel ??= $this->versionLabel($publishedRevision, $publishedAt);
        $contentHash = ComputeDocumentContentHashAction::run($content);

        $publication = $document->publications()
            ->where('version_label', $versionLabel)
            ->first();

        if ($publication instanceof DocumentPublication) {
            if (! hash_equals($publication->content_hash, $contentHash)) {
                throw new LogicException(sprintf(
                    'Document [%s] version [%s] has already been published with a different content hash.',
                    $document->key,
                    $versionLabel,
                ));
            }

            $this->activateDocument($document);

            return $publication;
        }

        $publication = $document->publications()->create([
            'published_revision_id' => $publishedRevision?->getKey(),
            'version_label' => $versionLabel,
            'content_hash' => $contentHash,
            'published_actor_type' => $this->morphType($publishedActor),
            'published_actor_id' => $publishedActor?->getKey(),
            'published_at' => $publishedAt,
            'metadata' => $metadata === [] ? null : $metadata,
        ]);

        $this->activateDocument($document);

        return $publication;
    }

    private function activateDocument(Document $document): void
    {
        if ($document->status === DocumentStatusEnum::Active) {
            return;
        }

        $document->forceFill(['status' => DocumentStatusEnum::Active])->save();
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
