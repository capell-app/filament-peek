---
name: capell-local-instances
description: Use when setting up, refreshing, or debugging local Capell Laravel instances for individual package testing, especially Devilbox browser viewing, disposable demo apps, package install checks, and symlinked capell-4/capell-packages-4 development.
---

# Capell Local Instances

Use this skill when a task needs a real Laravel/Capell app instead of Testbench package tests.

## Default Decision

- For package code behaviour, stay in `capell-packages-4` and run focused Pest:

```bash
vendor/bin/pest packages/<package>/tests --configuration=phpunit.xml
```

- For browser, Filament, install-flow, route, asset, or public frontend checks, use a disposable Laravel app with local path repositories.
- Prefer Devilbox when the user needs browser viewing through a stable local vhost. Use PHP's built-in server only for quick non-Devilbox throwaway checks.

## Canonical Local Paths

- Package repo: `/Users/ben/Sites/packages/capell/capell-packages-4`
- Core app/package repo: `/Users/ben/Sites/packages/capell/capell-4`
- Existing non-Devilbox demo harness: `/Users/ben/Sites/packages/capell/capell-package-demo-audit`
- Devilbox root: `/Users/ben/devilbox`
- Devilbox web root: `/Users/ben/devilbox/data/www`
- Devilbox container project path: `/shared/httpd/<project>`
- Devilbox vhost suffix: `.test`

Expose the local Capell repos inside Devilbox once per machine:

```bash
ln -s /Users/ben/Sites/packages/capell/capell-packages-4 /Users/ben/devilbox/data/www/capell-packages-4
ln -s /Users/ben/Sites/packages/capell/capell-4 /Users/ben/devilbox/data/www/capell-4
```

Skip a symlink if it already exists.

## Devilbox App Pattern

Create or refresh apps under `/Users/ben/devilbox/data/www/<app-name>` so Devilbox HTTPD can serve them.
Use names that identify the target package, for example `capell-blog-test`.

From the Devilbox root:

```bash
cd /Users/ben/devilbox
docker compose ps || docker-compose ps
```

Use whichever compose form succeeds for the rest of the task:

```bash
docker compose up -d
```

Run app commands inside the PHP container:

```bash
cd /Users/ben/devilbox
docker compose exec php bash -lc 'cd /shared/httpd/<app-name> && php -v'
```

Commands run through `docker compose exec php` create files as `root`, while PHP-FPM runs as the `devilbox` user. After Composer, Artisan, npm, or cache/build commands, make Laravel writable paths web-writable for disposable local apps:

```bash
docker compose exec php bash -lc 'cd /shared/httpd/<app-name> && chmod -R ugo+rwX storage bootstrap/cache public/build 2>/dev/null || true'
```

Devilbox serves `/Users/ben/devilbox/data/www/<app-name>/htdocs` as `http://<app-name>.test`.
For Laravel, make `htdocs` point at the app `public` directory when it does not already exist:

```bash
cd /Users/ben/devilbox/data/www/<app-name>
ln -s public htdocs
```

If the URL does not resolve, inspect `/Users/ben/devilbox/.env` and `/Users/ben/devilbox/hosts` before changing the documented suffix.

Add the vhost to the host machine before browser testing:

```bash
sudo sh -c 'printf "\n127.0.0.1 <app-name>.test\n" >> /etc/hosts'
```

Prefer checking first to avoid duplicates:

```bash
grep '<app-name>.test' /etc/hosts
```

Only edit `/etc/hosts` with explicit user approval when acting as an agent, because it is a system file.

## Create A Disposable Capell App

Run from `/Users/ben/devilbox/data/www` or `/Users/ben/Sites/packages/capell` depending on whether Devilbox viewing is required.
For Devilbox apps, run Composer and Artisan inside the PHP container once the directory exists so PHP extensions, service hostnames, and absolute paths match the web runtime.

```bash
composer create-project laravel/laravel <app-name>
cd <app-name>
```

Patch the app `composer.json` to include local symlink repositories before requiring Capell packages. Inside Devilbox, use `/shared/httpd/...` paths:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/shared/httpd/capell-packages-4/packages/*",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "/shared/httpd/capell-4/packages/*",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "vcs",
            "url": "https://github.com/fdemb/laravel-authentication-log"
        },
        {
            "type": "vcs",
            "url": "https://github.com/howdu/filament-adjacency-list"
        }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Equivalent command form:

```bash
composer config minimum-stability dev
composer config prefer-stable true
composer config repositories.capell-packages path /shared/httpd/capell-packages-4/packages/*
composer config repositories.capell-core path /shared/httpd/capell-4/packages/*
composer config repositories.auth-log-fork vcs https://github.com/fdemb/laravel-authentication-log
composer config repositories.adjacency-list-fork vcs https://github.com/howdu/filament-adjacency-list
```

Install the Capell baseline:

```bash
composer require capell-app/core:4.x-dev capell-app/admin:4.x-dev capell-app/frontend:4.x-dev capell-app/installer:4.x-dev capell-app/marketplace:4.x-dev -W
php artisan filament:install --panels
```

Use SQLite for disposable browser harnesses unless a package specifically needs MySQL:

```bash
touch database/database.sqlite
```

Set `.env`:

```dotenv
APP_URL=http://<app-name>.test
DB_CONNECTION=sqlite
DB_DATABASE=/shared/httpd/<app-name>/database/database.sqlite
```

For host-side commands outside Devilbox, use the absolute host database path instead.

Run the baseline installer:

```bash
php artisan capell:install --demo --package-mode=core --theme=none --url="${APP_URL}" --name="Demo Admin" --email=admin@example.test --password=password --clear-cache --install-welcome-route --no-interaction
php artisan capell:frontend-install
npm install
npm run build
```

Build the Filament admin theme too. Capell patches the panel provider with `->viteTheme('resources/css/filament/admin/theme.css', 'build/filament')`, so a fresh Laravel app needs a Filament theme CSS file and a separate Vite build for `public/build/filament/manifest.json`.

Create `resources/css/filament/admin/theme.css`:

```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';

@source '../../../../vendor/capell-app/admin/resources/views/**/*.blade.php';
@source '../../../../storage/capell/tailwind-classes.txt';
@source '../../../../app/Filament/**/*';
@source '../../../../resources/views/filament/**/*';
```

Create `vite.filament.config.js`:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/filament/admin/theme.css'],
            buildDirectory: 'build/filament',
            refresh: true,
        }),
        tailwindcss(),
    ],
})
```

Then run:

```bash
npm run build
npx vite build --config vite.filament.config.js
chmod -R ugo+rwX storage bootstrap/cache public/build public/vendor
php artisan optimize:clear
```

Admin login:

- Email: `admin@example.test`
- Password: `password`
- URL: `${APP_URL}/admin/login`

## Install One Package For Browser Testing

From the disposable app:

```bash
composer require capell-app/<package>:4.x-dev -W --no-interaction
php artisan migrate --graceful --ansi
php artisan capell:extension-install capell-app/<package> --no-interaction --ansi --url="${APP_URL}"
```

If the package has hard dependencies, install and mark those dependencies first. Blog commonly needs Layout Builder and related content/frontend packages to be marked installed as Capell extensions, not only Composer-installed.

When available, seed package demo data with the package's documented command, for example:

```bash
php artisan capell:demo-kit-full-demo --url="${APP_URL}" --user=admin@example.test --force --no-interaction
php artisan capell:<package>-demo --no-interaction
```

Use each package README for exact install/demo commands.

## Reset A Disposable App

For SQLite harnesses:

```bash
rm -f database/database.sqlite
touch database/database.sqlite
php artisan migrate --graceful --ansi
php artisan capell:install --demo --package-mode=core --theme=none --url="${APP_URL}" --name="Demo Admin" --email=admin@example.test --password=password --clear-cache --install-welcome-route --no-interaction
npm run build
```

If a package was added only for one check, remove it with Composer before resetting:

```bash
composer remove capell-app/<package> -W --no-interaction
```

## Verification

Minimum useful checks after setup:

```bash
php artisan about
php artisan capell:doctor
php artisan route:list --json
```

For command-line HTTP checks without editing `/etc/hosts`, force host resolution:

```bash
curl --resolve <app-name>.test:80:127.0.0.1 -I http://<app-name>.test/
curl --resolve <app-name>.test:80:127.0.0.1 -I http://<app-name>.test/admin/login
```

Then open in a browser after `/etc/hosts` contains the vhost:

- Frontend: `${APP_URL}`
- Admin login: `${APP_URL}/admin/login`

For browser testing, use the Codex Browser or Playwright. Confirm public pages do not expose frontend-authoring/editor data to anonymous users.

## Tested Baseline

Verified on 2026-05-19 with:

- Devilbox PHP 8.4 container and nginx HTTPD.
- Laravel `laravel/laravel` 13.7.0 resolving to Laravel framework 13.11.1.
- Capell baseline packages: `core`, `admin`, `frontend`, `installer`, `marketplace` at `4.x-dev` through local path repositories.
- Disposable app: `/Users/ben/devilbox/data/www/capell-skill-clean-test`.

Passing checks from that run:

```bash
php artisan about --only=environment
php artisan capell:doctor
php artisan route:list --json
curl --resolve capell-skill-clean-test.test:80:127.0.0.1 -I http://capell-skill-clean-test.test/
curl --resolve capell-skill-clean-test.test:80:127.0.0.1 -I http://capell-skill-clean-test.test/admin/login
```

`capell:doctor` passed after publishing frontend assets with `capell:frontend-install`. Both HTTP checks returned `200 OK` after writable permissions and the Filament theme build were added.

## Updating This Skill

When local setup changes, update this file first. Keep commands executable, paths concrete, and package-specific exceptions out of this skill unless they affect the general harness flow. Put package details in the package README instead.
