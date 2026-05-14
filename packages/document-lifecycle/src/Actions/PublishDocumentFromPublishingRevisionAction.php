<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Actions;

use Capell\DocumentLifecycle\Models\Document;
use Capell\DocumentLifecycle\Models\DocumentPublication;
use Capell\PublishingStudio\Models\PublishingRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lorisleiva\Actions\Concerns\AsAction;

final class PublishDocumentFromPublishingRevisionAction
{
    use AsAction;

    public function handle(PublishingRevision $revision): ?DocumentPublication
    {
        if ($revision->after_payload === null) {
            return null;
        }

        $document = $this->documentForRevision($revision);

        if (! $document instanceof Document) {
            return null;
        }

        $this->syncDocumentableTarget($document, $revision);

        return PublishDocumentAction::run(
            document: $document->refresh(),
            content: $revision->after_payload,
            publishedRevision: $revision,
            publishedActor: $revision->actor instanceof Model ? $revision->actor : null,
            publishedAt: $revision->created_at,
            metadata: [
                'event_type' => $revision->event_type->value,
                'publishing_revision_uuid' => $revision->uuid,
                'revisionable_type' => $revision->revisionable_type,
                'revisionable_id' => $revision->revisionable_id,
                'revisionable_uuid' => $revision->revisionable_uuid,
                'workspace_id' => $revision->workspace_id,
                'version_id' => $revision->version_id,
            ],
        );
    }

    private function documentForRevision(PublishingRevision $revision): ?Document
    {
        $documentableIds = array_values(array_unique(array_filter([
            (int) $revision->revisionable_id,
            $this->beforePayloadId($revision),
        ])));

        if ($documentableIds === []) {
            return null;
        }

        return Document::query()
            ->whereIn('documentable_type', $this->documentableTypes($revision->revisionable_type))
            ->whereIn('documentable_id', $documentableIds)
            ->first();
    }

    /**
     * @return array<int, string>
     */
    private function documentableTypes(string $revisionableType): array
    {
        $alias = array_search($revisionableType, Relation::morphMap(), true);

        return array_values(array_unique(array_filter([
            $revisionableType,
            is_string($alias) ? $alias : null,
        ])));
    }

    private function beforePayloadId(PublishingRevision $revision): ?int
    {
        $id = $revision->before_payload['id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function syncDocumentableTarget(Document $document, PublishingRevision $revision): void
    {
        if ((int) $document->documentable_id === (int) $revision->revisionable_id) {
            return;
        }

        $document
            ->forceFill(['documentable_id' => $revision->revisionable_id])
            ->save();
    }
}
