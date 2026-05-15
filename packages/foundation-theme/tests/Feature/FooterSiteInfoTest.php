<?php

declare(strict_types=1);

use Capell\Core\Models\Page;
use Capell\Core\Models\PageUrl;
use Capell\Core\Models\Site;
use Capell\Core\Models\SiteDomain;
use Capell\Core\Models\Translation;
use Illuminate\Support\Facades\Blade;

it('renders site contact details from meta', function (): void {
    Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/components', 'capell');

    $siteDomain = new SiteDomain([
        'domain' => 'example.test',
        'path' => null,
        'scheme' => 'https',
    ]);

    $site = new Site([
        'name' => 'Capell Ruby',
        'meta' => [
            'business_name' => 'Capell Ltd',
            'email' => 'hello@example.test',
            'phone' => '+44 20 7946 0958',
        ],
    ]);
    $site->setRelation('siteDomain', $siteDomain);
    $site->setRelation('translation', new Translation(['title' => 'Capell Ruby']));

    $contactPage = new Page(['name' => 'Contact']);
    $contactPageUrl = new PageUrl(['url' => '/contact']);
    $contactPageUrl->setRelation('siteDomain', $siteDomain);

    $contactPage->setRelation('pageUrl', $contactPageUrl);
    $contactPage->setRelation('translation', new Translation([
        'title' => 'Talk to us',
        'meta' => ['label' => 'Talk to us'],
    ]));

    test()->blade(
        '<x-capell::footer.site-info :site="$site" :contact-page="$contactPage" />',
        [
            'site' => $site,
            'contactPage' => $contactPage,
        ],
    )
        ->assertSee('Capell Ltd')
        ->assertSee('mailto:hello@example.test', false)
        ->assertSee('tel:+442079460958', false)
        ->assertSee('Talk to us');
});
