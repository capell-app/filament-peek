# Capell

[![Latest Version on Packagist](https://img.shields.io/packagist/v/capell-app/capell.svg?style=flat-square)](https://packagist.org/packages/capell-app/capell)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/capell-app/capell/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/capell-app/capell/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/capell-app/capell/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/capell-app/capell/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/capell-app/capell.svg?style=flat-square)](https://packagist.org/packages/capell-app/capell)

Filament CMS aimed at medium to large websites with organized content.

### Requirements

- PHP 8.1, 8.2
- Filament 3

### Includes

- [Admin](#admin) - multiple site CMS for managing pages and content. Built with Filament.

Optional: use with or without the admin panel.

- [Site](#frontend-site) - whitelabel HTML generated cached site.

Extra Modules:

- [Blog](#blog) - for managing blog posts.

## Features

- Multi-lingual (everything is translatable including URLs)
  E.g: https://example.com/fr OR https://fr.example.com
- Multi-site (via subdomain Or path after domain)
- HTML Page cache for blazing fast rendering performance
- Layout builder with drag and drop widget support. Organize your content and resources in reusable layouts or specific to a page.
    - Widgets
    - Widget resources added directly to a widget to be reusable across multiple layouts.
      OR added to a specific page.
- Contents: manage your content separately to individual pages
- Unlimited parent page hierarchy depth e.g: /parent/page/child
- Page Redirects. Auto detect when a url is changed and add redirect to the new URL.
- Fully hierarchical page structure with unlimited depth. Change a parent URL and all child pages will be updated automatically.
- Extendable with Page, Site, Widget, Theme, Layout and Content types. Including Filament schemas.
- Content Manager allows for content to be separated from the individual pages.
- Multilingual - Translate content and locale URLs without limitations.
- Themes
- Tags
- Auto generated sitemap both html and xml.
- SEO friendly URLs
- Image management using Curator using Glide for image resizing and manipulation.
- Extendable page types
- Authentication logs and auditing
- Fully editable page URLs with redirects or duplicate
- Friendly 404 error page
- Admin toolbar for easy access to edit page and clear html cache. (only shows if logged into admin)
- HTML Page editor with TinyMCE
- Page draft with versioning.
- Audit log history of changes
- Page views counter.
- Searchable page content with search log
- Site:

    - Image Lightbox

- Filament admin adapts for clean single language and site, to a multi-lingual and multi-site admin panel.

Optional:

- Blog articles
- Archives

---

## Install

Requirements:

- PHP: 8.2, 8.3, 8.4
- Database: MySQL, MariaDB or SQLite
- Laravel: 10, 11, 12 [https://laravel.com/docs/12.x/installation](https://laravel.com/docs/12.x/installation)
- Filament: 3 [https://filamentphp.com/docs/installation](https://filamentphp.com/docs/installation)

If you've not already installed the Filament package, you can do so by running:
[https://filamentphp.com/docs/installation](https://filamentphp.com/docs/installation)

```bash
composer require capell-app/admin
```

Optional extensions:

- Site: the frontend site
- Blog: for managing blog posts

```bash
composer require capell-app/frontend \
  capell-app/blog
```

Edit your `composer.json` file to include the following:

`````json
{
  "minimum-stability": "dev",
}
```

### For Laravel 12 some core packages are not compatible yet and need to use forks.

```bash
{
    "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/howdu/filament-navigation"
            },
            {
                "type": "vcs",
                "url": "https://github.com/capell-app/core"
            },
            {
                "type": "vcs",
                "url": "https://github.com/capell-app/admin"
            },
            {
                "type": "vcs",
                "url": "https://github.com/capell-app/frontend"
            },
            {
                "type": "vcs",
                "url": "https://github.com/capell-app/blog"
            },
            {
                "type": "vcs",
                "url": "https://github.com/laravel-shift/blade-country-flags.git"
            }
    ],
    "require": {
        "stijnvanouplines/blade-country-flags": "dev-l12-compatibility",
        "ryangjchandler/filament-navigation": "dev-chore/add-laravel-12-support"
    }
}
```

### For local development

```bash
"repositories": [
    {
        "type": "path",
        "url": "../capell-app/packages/*",
        "symlink": true
    }
],
```

## Setup

### 1. Admin Panel

Edit your Filament admin panel e.g `app/Providers/Filament/AdminPanelProvider.php` file to include the following:

```php
->navigationItems(\Capell\Admin\Facades\CapellAdmin::getNavigationItems())
->navigationGroups(\Capell\Admin\Facades\CapellAdmin::getNavigationGroups())
->plugin(\Capell\Admin\CapellAdminPlugin::make())
->widgets([
    \Capell\Admin\Filament\Widgets\AuthenticationLogsWidget::class,
    \Capell\Admin\Filament\Widgets\LatestPagesWidget::class,
    \Capell\Admin\Filament\Widgets\TotalPageViewsChartWidget::class,
    \Capell\Admin\Filament\Widgets\TotalVisitorsChartWidget::class,
])
```

### 2. Create HTML Cache Directory

````bash
Add a new storage disk into `config/filesystems.php` and make sure `/public/page-cache/` directory is writable.

```php
return [
  'disks' => [
    'page_cache' => [
        'driver' => 'local',
        'root' => public_path('page-cache'),
        'throw' => false,
    ],
    // ...
  ]
];
`````

### 3. Edit User Model (app/Models/User.php).

```php
use BezhanSalleh\FilamentShield\Support\Utils;
use Capell\Core\Models\Media;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, Auditable
{
/** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use AuthenticationLoggable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'profile_image_id',
    ];

    public function profileImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'profile_image_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(Utils::getSuperAdminName()) || $this->hasRole(Utils::getPanelUserRoleName());
    }
```

### 4. Install the package

```bash
php artisan capell-admin:install;
```

This will automatically install extra packages if they are already installed via composer. Alternatively you can install them manually.

```bash
php artisan capell-frontend:install;
php artisan capell-blog:install;
```

### 5. Publish the Filament theme `tailwind.config.js`

```json
{
    "content": [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/awcodes/filament-curator/resources/**/*.blade.php",
        "./vendor/capell-app/admin/resources/**/*.blade.php",
        "./vendor/capell-app/admin/src/Enums/ModalWidthEnum.php",
        "./vendor/capell-app/blog/resources/**/*.blade.php",
        "./vendor/cms-multi/filament-clear-cache/resources/**/*.blade.php",
        "./vendor/capell-app/core/resources/**/*.blade.php",
        "./vendor/codewithdennis/filament-simple-alert/resources/**/*.blade.php",
        "./vendor/filament/**/*.blade.php"
    ]
}
```

#### Remove default welcome route

If you have a default welcome route in your `routes/web.php` file, remove it.

#### Allow user to edit self

Edit `app/Policies/UserPolicy.php` file to allow user to edit their own profile.

```php
/**
 * Determine whether the user can view the model.
 *
 * @param  \App\Models\User  $user
 * @param  \App\Models\User  $model
 * @return bool
 */
public function view(User $user, User $model): bool
{
    return $user->id === $model->id || $user->can('view_user');
}

/**
 * Determine whether the user can update the model.
 *
 * @param  \App\Models\User  $user
 * @param  \App\Models\User  $model
 * @return bool
 */
public function update(User $user, User $model): bool
{
    return $user->id === $model->id || $user->can('update_user');
}
```

#### Serve static html page

For incredibly fast performance (optional but recommended). It's recommended to bypass PHP and let your server render the html directly.
More info see: [https://github.com/JosephSilber/page-cache](https://github.com/JosephSilber/page-cache)

See [Server Config](docs/server-config.md) for more details.

## Setup Task Scheduling

Setup Laravel [queue worker using supervisor](https://laravel.com/docs/10.x/queues#supervisor-configuration)

## Setup Task Scheduling (cron)

Setup Laravel [task scheduler](https://laravel.com/docs/10.x/scheduling#running-the-scheduler)

There's a scheduled job which will automatically keep the static HTML pages fresh.

### Optimize the application

```bash
php artisan optimize
```

---

## Upgrade

```bash
composer update

php artisan capell-admin:upgrade

php artisan capell-frontend:upgrade
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Contributors

<!-- readme: collaborators,contributors -start -->
<table>
	<tbody>
		<tr>
            <td align="center">
                <a href="https://github.com/howdu">
                    <img src="https://avatars.githubusercontent.com/u/533658?v=4" width="100;" alt="howdu"/>
                    <br />
                    <sub><b>Beej</b></sub>
                </a>
            </td>
            <td align="center">
                <a href="https://github.com/oddvalue">
                    <img src="https://avatars.githubusercontent.com/u/10127404?v=4" width="100;" alt="oddvalue"/>
                    <br />
                    <sub><b>oddvalue</b></sub>
                </a>
            </td>
            <td align="center">
                <a href="https://github.com/capell-app">
                    <img src="https://avatars.githubusercontent.com/u/118010471?v=4" width="100;" alt="capell-app"/>
                    <br />
                    <sub><b>capell-app</b></sub>
                </a>
            </td>
		</tr>
	<tbody>
</table>
<!-- readme: collaborators,contributors -end -->

## Special Thanks

It would not of been possible without the help of other open source software.

- [Filament](https://github.com/filamentphp/filament)
- [Filament Curator + Badge Column](https://github.com/awcodes/filament-curator)
- [Filament Navigation](https://github.com/ryangjchandler/filament-navigation)
- [Filament TinyMce HTML Editor](https://github.com/mohamedsabil83/filament-forms-tinyeditor)
- [Filament Title with Slug](https://github.com/camya/filament-title-with-slug)
- [Filament Icon Picker](https://github.com/LukasFreyCZ/filament-icon-picker)
- [Spatie Laravel Tags + Translatable](https://github.com/spatie/laravel-tags)
- Not forgetting the amazing TALL stack: TailwindCSS, AlpineJS, Laravel and Livewire
- Demo Images from [unsplash.com](https://unsplash.com)
