<?php

declare(strict_types=1);

namespace Capell\Hero\View\Components\Widget;

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Frontend\Facades\Frontend;
use Capell\Hero\Actions\HeroWidgetHasPrimaryHeadingAction;
use Capell\Layout\Models\Content;
use Capell\Layout\View\Components\Widget\AbstractWidget;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

class Hero extends AbstractWidget
{
    protected static string $defaultView = 'capell-hero::components.widget.hero';

    public static function loadWidgetAssets(array &$morphRelations, ?Language $language = null): void
    {
        $related = fn (BuilderContract $query) => $query->with([
            'image',
            'page' => fn (BuilderContract $query) => $query->with([
                'translation' => fn (BuilderContract $query) => $query->with('language')
                    ->when($language, fn ($q) => $q->where('language_id', $language->id)),
                'pageUrl' => fn (BuilderContract $query) => $query->with('siteDomain')
                    ->when($language, fn ($q) => $q->where('language_id', $language->id)),
                'site',
            ]),
        ])
            ->withWhereHas('translation', fn (BuilderContract $query) => $query->with('language'));

        $morphRelations[Content::class]['related'] = $related;
        $morphRelations[Page::class]['related'] = $related;
    }

    protected function mountWidget(): void
    {
        $page = Frontend::page();

        if (
            empty($page->translation->meta['hero']) &&
            ! $this->widget->translation?->content &&
            $this->widget->assets->isEmpty()
        ) {
            $this->skipRender = true;

            return;
        }

        HeroWidgetHasPrimaryHeadingAction::run($this->widget, $page);
    }
}
