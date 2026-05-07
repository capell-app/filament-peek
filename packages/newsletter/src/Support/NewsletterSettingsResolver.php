<?php

declare(strict_types=1);

namespace Capell\Newsletter\Support;

use Capell\Newsletter\Enums\ResubscribePolicy;
use Capell\Newsletter\Settings\NewsletterSettings;
use Spatie\LaravelSettings\Exceptions\MissingSettings;

class NewsletterSettingsResolver
{
    public function resubscribePolicyForSite(int $siteId): ResubscribePolicy
    {
        try {
            $settings = resolve(NewsletterSettings::class);
            $sitePolicies = is_array($settings->site_resubscribe_policies) ? $settings->site_resubscribe_policies : [];
            $sitePolicy = $sitePolicies[(string) $siteId] ?? null;

            if (is_string($sitePolicy) && $sitePolicy !== '') {
                return ResubscribePolicy::tryFrom($sitePolicy) ?? ResubscribePolicy::RequireDoubleOptIn;
            }

            return ResubscribePolicy::tryFrom($settings->default_resubscribe_policy) ?? ResubscribePolicy::RequireDoubleOptIn;
        } catch (MissingSettings) {
            $value = config('capell-newsletter.resubscribe_policy', ResubscribePolicy::RequireDoubleOptIn->value);

            return is_string($value)
                ? ResubscribePolicy::tryFrom($value) ?? ResubscribePolicy::RequireDoubleOptIn
                : ResubscribePolicy::RequireDoubleOptIn;
        }
    }
}
