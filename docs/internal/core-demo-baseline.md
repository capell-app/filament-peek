# Core Demo Baseline

Captured on 2026-05-19 from the isolated package demo harness.

## Harness

- App path: `/Users/ben/Sites/packages/capell/capell-package-demo-audit`
- App URL: `http://127.0.0.1:8000`
- Runtime: PHP built-in server with Homebrew PHP 8.5.5
- Admin account: `admin@example.test` / `password`

## Installed Baseline Packages

- `capell-app/core`
- `capell-app/admin`
- `capell-app/frontend`
- `capell-app/installer`
- `capell-app/marketplace`

No package from `/Users/ben/Sites/packages/capell/capell-packages-4/packages` was installed for this baseline capture.

## Verified URLs

- Public frontend: `http://127.0.0.1:8000` returned `HTTP/1.1 200 OK`
- Admin login: `http://127.0.0.1:8000/admin/login` returned `HTTP/1.1 200 OK`

## Admin Surface Baseline

The structured baseline is stored in [core-admin-menu-baseline.json](core-admin-menu-baseline.json).

- Resources: 12
- Pages: 11
- Dashboard widgets: 10
- Admin/Filament routes: 43

Package audits should diff their installed admin resources, pages, widgets, and routes against this file before screenshots are captured. Any admin surface outside the target package and its hard dependencies should be treated as an install isolation issue.

## Frontend Health Concern

The app boots, but `php artisan capell:doctor` reports missing generated Capell frontend assets:

- `public/vendor/capell-frontend/manifest.json`
- Generated Capell frontend Tailwind CSS

`php artisan capell:frontend-after-install --no-interaction` is currently blocked by `Target class [capell.tailwind.generator] does not exist.` Frontend screenshots are suitable for route and content verification, but final visual baselines should revisit this asset generator binding before publication.
