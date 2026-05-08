<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Resources\Pages\Widgets;

use Capell\Admin\Filament\Concerns\HasBlankPlaceholder;
use Capell\Admin\Support\SiteScope;
use Capell\Core\Enums\TranslatableType;
use Capell\Core\Models\Page;
use Capell\Core\Models\Translation;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class ListPageSeoAuditWidget extends Widget
{
    use HasBlankPlaceholder;

    protected int|string|array $columnSpan = ['default' => 'full'];

    protected string $view = 'capell-seo-suite::filament.widgets.seo-audit-list';

    /**
     * @return array{total: int, missingDescription: int, titleIssues: int, duplicateTitles: int}
     */
    #[Computed]
    public function totals(): array
    {
        /** @var class-string<Page> $pageModel */
        $pageModel = Page::class;

        $pageIds = SiteScope::applyForCurrentActor($pageModel::query())->pluck('id');

        if ($pageIds->isEmpty()) {
            return ['total' => 0, 'missingDescription' => 0, 'titleIssues' => 0, 'duplicateTitles' => 0];
        }

        /** @var class-string<Translation> $translationModel */
        $translationModel = Translation::class;

        $translations = $translationModel::query()
            ->where('translatable_type', TranslatableType::Page->value)
            ->whereIn('translatable_id', $pageIds)
            ->get(['translatable_id', 'meta']);

        $grouped = $translations->groupBy('translatable_id');

        $missingDescription = 0;
        $titleIssues = 0;
        $titleIndex = [];

        foreach ($grouped as $pageId => $pageTranslations) {
            $allMissingDescription = $pageTranslations->every(
                fn (Translation $t): bool => (($t->meta ?? [])['description'] ?? '') === '',
            );

            if ($allMissingDescription) {
                $missingDescription++;
            }

            $hasTitleIssue = false;
            foreach ($pageTranslations as $t) {
                $title = ($t->meta ?? [])['title'] ?? '';

                if ($title === '') {
                    $hasTitleIssue = true;
                    break;
                }

                $len = mb_strlen((string) $title);

                if ($len > 60 || $len < 30) {
                    $hasTitleIssue = true;
                    break;
                }

                $titleIndex[$title][] = (int) $pageId;
            }

            if ($hasTitleIssue) {
                $titleIssues++;
            }
        }

        $duplicateTitles = collect($titleIndex)
            ->filter(fn (array $ids): bool => count(array_unique($ids)) > 1)
            ->sum(fn (array $ids): int => count(array_unique($ids)));

        return [
            'total' => $pageIds->count(),
            'missingDescription' => $missingDescription,
            'titleIssues' => $titleIssues,
            'duplicateTitles' => $duplicateTitles,
        ];
    }
}
