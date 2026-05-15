<?php

declare(strict_types=1);

namespace Capell\Hero\View\Components\Element;

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Frontend\Facades\Frontend;
use Capell\LayoutBuilder\Actions\HeroElementHasPrimaryHeadingAction;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Model;

class Hero extends AbstractElement
{
    protected static string $defaultView = 'capell-hero::components.element.hero';

    public static function loadElementAssets(array &$morphRelations, ?Language $language = null): void
    {
        $morphRelations[Page::class]['related'] = fn (BuilderContract $query): BuilderContract => $query->with(Page::getMorphRelations($language))
            ->withWhereHas('translation', fn (BuilderContract $query): BuilderContract => $query->with('language'));

        foreach (array_keys($morphRelations) as $assetModel) {
            if ($assetModel === Page::class) {
                continue;
            }

            if (! is_a($assetModel, Model::class, true)) {
                continue;
            }

            if (! method_exists($assetModel, 'getMorphRelations')) {
                continue;
            }

            $morphRelations[$assetModel]['related'] = fn (BuilderContract $query): BuilderContract => $query
                ->with($assetModel::getMorphRelations($language))
                ->withWhereHas('translation', fn (BuilderContract $query): BuilderContract => $query->with('language'));
        }
    }

    protected function mountElement(): void
    {
        $page = Frontend::page();

        $hasHero = isset($page->translation->meta['hero']) && filled($page->translation->meta['hero']);

        if (
            $hasHero === false &&
            blank($this->element->translation?->content) &&
            $this->element->assets->isEmpty()
        ) {
            $this->skipRender = true;

            return;
        }

        HeroElementHasPrimaryHeadingAction::run($this->element, $page);
    }
}
