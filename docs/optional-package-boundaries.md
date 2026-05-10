# Optional Package Boundaries

Composer availability is not Capell extension availability. A package class can autoload while the extension is not installed, disabled, or missing its tables.

Do not use `class_exists()` as the only guard before calling optional Capell package runtime code:

```php
if (class_exists(Navigation::class)) {
    Navigation::query()->first();
}
```

Use Capell's installed-state check first:

```php
if (CapellCore::isPackageInstalled('capell-app/navigation') && class_exists(Navigation::class)) {
    Navigation::query()->first();
}
```

If the code can run during install, upgrade, diagnostics, or another partial database state, also guard the table before querying:

```php
if (
    CapellCore::isPackageInstalled('capell-app/navigation')
    && Schema::hasTable('navigations')
    && class_exists(Navigation::class)
) {
    Navigation::query()->first();
}
```

This applies to models, Actions, Blade components, Filament fields, listeners, render hooks, and service-provider registrations that touch package runtime behavior.

`class_exists()` is still appropriate for non-Capell PHP/library capabilities, dynamic configured classes, autoload priming before cache deserialization, and defensive validation after the Capell package has already been proven installed.
