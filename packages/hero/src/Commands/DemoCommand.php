<?php

declare(strict_types=1);

namespace Capell\Hero\Commands;

use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Hero\Actions\AddHeroToLayoutAction;
use Capell\Hero\Actions\CreateHeroWidgetAction;
use Capell\Layout\Services\Creator\DemoCreator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

use function Laravel\Prompts\multisearch;

class DemoCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts demo hero content into the selected site(s).';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capell-hero:demo {--sites=}';

    private DemoCreator $demoCreator;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('sites')) {
            $sites = is_string($this->option('sites'))
                ? [$this->option('sites')]
                : $this->option('sites');

            $siteIds = Site::query()
                ->whereIn('id', $sites)
                ->orWhereIn('name', $sites)
                ->pluck('id')
                ->all();

            if (! $siteIds) {
                $this->error('No valid sites found for the provided identifiers: ' . implode(', ', $sites));

                return Command::FAILURE;
            }
        } else {
            $sites = CapellCore::getModel(ModelEnum::Site)::query()
                ->select(['id', 'name']);

            if ($sites->count() === 1) {
                $siteIds = $sites->pluck('id')->toArray();
            } else {
                $siteIds = multisearch(
                    'Select a site to insert demo pages',
                    options: fn (string $search) => CapellCore::getModel(ModelEnum::Site)::query()
                        ->when(
                            mb_strlen($search) > 0,
                            fn (Builder $query) => $query->where('name', 'like', sprintf('%%%s%%', $search))
                        )
                        ->get()
                        ->mapWithKeys(fn (Site $site): array => [$site->id => $site->name])
                        ->all(),
                    validate: [
                        'required',
                        'array',
                        'min:1',
                    ],
                );
            }
        }

        $sites = Site::query()->with(['language', 'languages'])->whereIn('id', $siteIds)->get();

        throw_if($sites->isEmpty(), new Exception('Unable to find any sites for the provided identifiers: ' . implode(', ', $siteIds)));

        $this->demoCreator = app(DemoCreator::class);

        $heroWidget = CreateHeroWidgetAction::run();

        foreach ($sites as $site) {
            $this->newLine();
            $this->line(sprintf('Selected site: %s', $site->name));

            $homepage = CapellCore::getModel(ModelEnum::Page)::getSiteHomePage($site);

            $homepage->loadMissing('layout');

            AddHeroToLayoutAction::run($homepage->layout);

            $this->demoCreator->createContentsWidget($heroWidget, $homepage, container: 'hero');

            if (CapellCore::hasPackage('capell-blog')) {
                $blogPage = CapellCore::getModel(ModelEnum::Page)::query()
                    ->with('translations')
                    ->where('site_id', $site->id)
                    ->whereRelation('type', 'key', 'blog')
                    ->first();

                if ($blogPage instanceof Page) {
                    foreach ($blogPage->translations as $translation) {
                        $meta = $translation->meta;
                        $meta['hero'] = '<h1>' . __('capell-blog::generic.latest_articles') . '</h1><p>' . __('capell-blog::generic.blog_intro') . '</p>';

                        $translation->update(['meta' => $meta]);
                    }
                }
            }

            $this->line('Demo hero content has been successfully created for site: ' . $site->name);
        }

        $this->line('Hero demo content inserted successfully.');

        return Command::SUCCESS;
    }
}
