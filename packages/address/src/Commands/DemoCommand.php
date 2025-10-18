<?php

declare(strict_types=1);

namespace Capell\Address\Commands;

use Capell\Address\Models\Address;
use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Site;
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
    protected $description = 'Inserts demo address content into the selected site(s).';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capell-address:demo {--sites=}';

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

        foreach ($sites as $site) {
            $this->newLine();
            $this->line(sprintf('Selected site: %s', $site->name));

            $meta = $site->meta ?? [];

            $address = $this->setupAddress();

            $meta['address_id'] = $address->id;

            $site->update(['meta' => $meta]);

            $this->line('Demo address content has been successfully created for site: ' . $site->name);
        }

        $this->line('Hero demo content inserted successfully.');

        return Command::SUCCESS;
    }

    private function setupCountry()
    {
        $countryModel = CapellCore::getModel(ModelEnum::Country);

        return $countryModel::firstOrCreate(
            ['iso2' => 'US'],
            [
                'name' => 'United States',
                'iso2' => 'US',
                'iso3' => 'USA',
                'language_id' => CapellCore::getModel(ModelEnum::Language)::where('code', 'en')->first()->id,
            ],
        );
    }

    private function setupAddress(): Address
    {
        return CapellCore::getModel(ModelEnum::Address)::firstOrCreate(
            [
                'line1' => '123 Main St',
                'city' => 'Anytown',
                'postal_code' => '12345',
                'country_id' => $this->setupCountry()->id,
            ],
            [
                'name' => 'Headquarters',
                'line2' => 'Suite 100',
                'state' => 'CA',
                'meta' => [
                    'latitude' => 34.0522,
                    'longitude' => -118.2437,
                ],
            ],
        );
    }
}
