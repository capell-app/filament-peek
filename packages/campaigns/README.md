# Campaigns

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **growth** · Contexts: **admin, frontend** · Product group: **Capell Growth**

## What This Plugin Adds

Campaigns adds campaign groups, landing pages, CTA blocks, conversion goals, UTM attribution, and conversion reporting to Capell.

- Campaign Filament resources for groups, landing pages, goals, and CTA blocks.
- Campaign dashboard widgets.
- Page schema extender for campaign fields.
- Mosaic widget configurators for campaign hero, CTA, and lead form blocks.
- Conversion recording actions for page views, CTA clicks, and form submissions.

## Why It Matters

**For developers:** Connects Capell pages, Forms, Analytics, and Mosaic through explicit actions and listener classes instead of inline resource logic.

**For teams:** Lets marketing and editorial teams connect landing pages to goals and see which campaigns convert.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Campaign groups index.
- Campaign landing pages index.
- Campaign conversion goals form.
- CTA block form.
- Campaign dashboard widgets.
- Frontend landing page with campaign widgets.

## Technical Shape

- CampaignsServiceProvider, AdminServiceProvider, and FrontendServiceProvider register package surfaces.
- Config file: capell-campaigns.php.
- Migrations create campaign groups, goals, landing pages, CTA blocks, and conversions.
- Filament resources cover each owned model.
- Listeners sync landing pages and form submission conversions.

## Data Model

- campaign_groups belong to sites.
- campaign_landing_pages belong to groups and target pages.
- campaign_conversion_goals define measurable outcomes.
- campaign_cta_blocks store CTA content.
- campaign_conversions connect goals, landing pages, analytics visits/events, and attribution JSON.

## Install Impact

- Adds campaign admin navigation and database tables.
- Adds campaign dashboard widgets.
- Adds config keys for conversion cookie, UTM keys, table names, and layout presets.
- May use Analytics events and Forms submissions when those packages are installed.
- No explicit public route is registered by this package.

## Commands

- `capell:campaigns-install-layouts {--force : Update existing campaign layouts}` (packages/campaigns/src/Console/Commands/InstallCampaignLayoutsCommand.php)

## Admin And Access

- CampaignConversionGoalResource (packages/campaigns/src/Filament/Resources/CampaignConversionGoals/CampaignConversionGoalResource.php)
- CreateCampaignConversionGoal (packages/campaigns/src/Filament/Resources/CampaignConversionGoals/Pages/CreateCampaignConversionGoal.php)
- EditCampaignConversionGoal (packages/campaigns/src/Filament/Resources/CampaignConversionGoals/Pages/EditCampaignConversionGoal.php)
- ListCampaignConversionGoals (packages/campaigns/src/Filament/Resources/CampaignConversionGoals/Pages/ListCampaignConversionGoals.php)
- CampaignCtaBlockResource (packages/campaigns/src/Filament/Resources/CampaignCtaBlocks/CampaignCtaBlockResource.php)
- CreateCampaignCtaBlock (packages/campaigns/src/Filament/Resources/CampaignCtaBlocks/Pages/CreateCampaignCtaBlock.php)
- EditCampaignCtaBlock (packages/campaigns/src/Filament/Resources/CampaignCtaBlocks/Pages/EditCampaignCtaBlock.php)
- ListCampaignCtaBlocks (packages/campaigns/src/Filament/Resources/CampaignCtaBlocks/Pages/ListCampaignCtaBlocks.php)
- CampaignGroupResource (packages/campaigns/src/Filament/Resources/CampaignGroups/CampaignGroupResource.php)
- CreateCampaignGroup (packages/campaigns/src/Filament/Resources/CampaignGroups/Pages/CreateCampaignGroup.php)
- EditCampaignGroup (packages/campaigns/src/Filament/Resources/CampaignGroups/Pages/EditCampaignGroup.php)
- ListCampaignGroups (packages/campaigns/src/Filament/Resources/CampaignGroups/Pages/ListCampaignGroups.php)
- CampaignLandingPageResource (packages/campaigns/src/Filament/Resources/CampaignLandingPages/CampaignLandingPageResource.php)
- CreateCampaignLandingPage (packages/campaigns/src/Filament/Resources/CampaignLandingPages/Pages/CreateCampaignLandingPage.php)
- EditCampaignLandingPage (packages/campaigns/src/Filament/Resources/CampaignLandingPages/Pages/EditCampaignLandingPage.php)
- ListCampaignLandingPages (packages/campaigns/src/Filament/Resources/CampaignLandingPages/Pages/ListCampaignLandingPages.php)

- Gate: CampaignOverviewStatsWidget: `admin`, `super_admin`
- Gate: TopCampaignsWidget: `admin`, `super_admin`
- Gate: TopLandingPagesWidget: `admin`, `super_admin`

## Common Pitfalls

- Install dependent packages before expecting attribution from forms or analytics.
- Check UTM keys before launch.
- Create conversion goals before reporting on landing page success.

## Quick Start

1. Install the package with `composer require capell-app/campaigns`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../analytics/README.md](../analytics/README.md)
- [../forms/README.md](../forms/README.md)
- [../mosaic/README.md](../mosaic/README.md)
