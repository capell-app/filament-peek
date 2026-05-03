# Package Boost Skills Design

## Goal

Create one very short Laravel Boost skill per Capell package. Each skill should help an AI agent quickly understand what the package does and how to edit it safely, without spending tokens on generic Capell rules.

## Approach

Use the tiny skill-only approach.

Each package skill will contain:

- YAML front matter with a package-specific name and description.
- One sentence describing the package's purpose.
- A compact `Look` section with source path, namespace, and docs entry points.
- A compact `Rules` section with only package-relevant guardrails.

Shared Capell conventions stay in `resources/boost/guidelines/core.blade.php` and repository guidance. Package skills should avoid repeating general standards unless the package has a specific reason.

## Package Coverage

Every package under `packages/*` with a `composer.json` should have:

- `resources/boost/skills/{skill-name}/SKILL.md`

Existing package Boost directories should be updated in place. Existing user work must not be reverted.

## Content Rules

- Keep each skill very short and focused.
- Aim for fast AI consumption over exhaustive documentation.
- Make each skill unique to the package's purpose.
- Point agents to `README.md`, `docs/`, and `src/` instead of duplicating details.
- Mention package-specific extension points, safety concerns, and test command where useful.
- Do not add Composer dependencies.

## Verification

Run the Boost resource test after edits:

```bash
vendor/bin/pest tests/Packages/BoostResourcesTest.php
```

If edits are broad but documentation-only, do not run the full suite unless package discovery or test coverage files are changed.
