<?php

declare(strict_types=1);

namespace Capell\Hero\Commands;

use Capell\Admin\Enums\LayoutEnum;
use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Site;
use Capell\Hero\Actions\AddHeroToLayoutAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

use function Laravel\Prompts\multisearch;

class InstallCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts hero';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capell-hero:install {--sites=}';

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

        $sites = Site::query()->with('languages')->whereIn('id', $siteIds)->get();

        throw_if($sites->isEmpty(), new Exception('Unable to find any sites for the provided identifiers: ' . implode(', ', $siteIds)));

        /** @var class-string<Layout> $layoutModel */
        $layoutModel = CapellCore::getModel(ModelEnum::Layout);

        $layoutModel::query()->whereNotIn('key', [
            LayoutEnum::Default->value,
            LayoutEnum::Home->value,
            LayoutEnum::Results->value,
            LayoutEnum::Tags->value,
        ])
            ->each(function (Layout $layout): void {
                AddHeroToLayoutAction::run($layout);
            });

        $this->line('Hero package installed successfully.');

        return Command::SUCCESS;
    }
}
