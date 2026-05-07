<?php

declare(strict_types=1);

namespace Capell\Newsletter\Actions;

use Capell\Newsletter\Models\Subscriber;
use Capell\Tags\Models\Tag;
use Lorisleiva\Actions\Concerns\AsAction;

class ApplyNewsletterTagsAction
{
    use AsAction;

    /**
     * @param  array<int, int|string>  $tagIds
     */
    public function handle(Subscriber $subscriber, array $tagIds, bool $replace = false): Subscriber
    {
        $newsletterTagType = config('capell-newsletter.newsletter_tag_type', 'newsletter');
        $validTagIds = Tag::query()
            ->whereIn('id', array_map(static fn (int|string $tagId): int => (int) $tagId, $tagIds))
            ->where('type', $newsletterTagType)
            ->pluck('id')
            ->map(static fn (mixed $tagId): int => (int) $tagId)
            ->all();

        if ($replace) {
            $subscriber->syncTagIds($validTagIds, is_string($newsletterTagType) ? $newsletterTagType : 'newsletter');

            return $subscriber->refresh();
        }

        foreach ($validTagIds as $tagId) {
            $subscriber->tags()->syncWithoutDetaching([$tagId]);
        }

        return $subscriber->refresh();
    }
}
