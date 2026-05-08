<?php

declare(strict_types=1);

use Capell\AccessGate\Tests\TestCase as AccessGateTestCase;
use Capell\Address\Tests\AddressTestCase;
use Capell\AgentBridge\Tests\TestCase as AgentBridgeTestCase;
use Capell\AIOrchestrator\Tests\AIOrchestratorTestCase;
use Capell\Blog\Tests\BlogTestCase;
use Capell\ContentSections\Tests\ContentSectionsTestCase;
use Capell\DemoKit\Tests\DemoKitTestCase;
use Capell\Deployments\Tests\TestCase as DeploymentsTestCase;
use Capell\Diagnostics\Tests\DiagnosticsTestCase;
use Capell\Events\Tests\EventsTestCase;
use Capell\FrontendOptimizer\Tests\FrontendOptimizerTestCase;
use Capell\Insights\Tests\InsightsTestCase;
use Capell\MediaAI\Tests\MediaAITestCase;
use Capell\MediaLibrary\Tests\MediaLibraryTestCase;
use Capell\Newsletter\Tests\NewsletterTestCase;
use Capell\PasswordPolicy\Tests\PasswordPolicyTestCase;
use Capell\PublishingStudio\Tests\PublishingStudioTestCase;
use Capell\Search\Tests\SearchTestCase;
use Capell\Tests\Packages\PackagesTestCase;
use Capell\Tests\Packages\UninstalledPackagesTestCase;
use Capell\WordPressImporter\Tests\WordPressImporterTestCase;

/**
 * @param  class-string  $testCase
 */
function extendCapellPackageTests(string $testCase, string $group, string $package): void
{
    pest()->extend($testCase)->group($group)->in("../packages/{$package}/tests", "../Packages/{$package}/tests");
}

extendCapellPackageTests(AddressTestCase::class, 'address', 'address');
extendCapellPackageTests(AccessGateTestCase::class, 'access-gate', 'access-gate');
extendCapellPackageTests(AgentBridgeTestCase::class, 'agent-bridge', 'agent-bridge');
extendCapellPackageTests(AIOrchestratorTestCase::class, 'ai-orchestrator', 'ai-orchestrator');
extendCapellPackageTests(BlogTestCase::class, 'blog', 'blog');
extendCapellPackageTests(ContentSectionsTestCase::class, 'content-sections', 'content-sections');
extendCapellPackageTests(DemoKitTestCase::class, 'demo-kit', 'demo-kit');
extendCapellPackageTests(DeploymentsTestCase::class, 'deployments', 'deployments');
extendCapellPackageTests(DiagnosticsTestCase::class, 'diagnostics', 'diagnostics');
extendCapellPackageTests(EventsTestCase::class, 'events', 'events');
extendCapellPackageTests(FrontendOptimizerTestCase::class, 'frontend-optimizer', 'frontend-optimizer');
extendCapellPackageTests(InsightsTestCase::class, 'insights', 'insights');
extendCapellPackageTests(PackagesTestCase::class, 'login-audit', 'login-audit');
extendCapellPackageTests(MediaAITestCase::class, 'media-ai', 'media-ai');
extendCapellPackageTests(MediaLibraryTestCase::class, 'media-library', 'media-library');
extendCapellPackageTests(NewsletterTestCase::class, 'newsletter', 'newsletter');
pest()->extend(PackagesTestCase::class)->in('Packages');
extendCapellPackageTests(PasswordPolicyTestCase::class, 'password-policy', 'password-policy');
extendCapellPackageTests(PublishingStudioTestCase::class, 'publishing-studio', 'publishing-studio');
extendCapellPackageTests(SearchTestCase::class, 'search', 'search');
extendCapellPackageTests(PackagesTestCase::class, 'theme-agency', 'theme-agency');
extendCapellPackageTests(PackagesTestCase::class, 'theme-corporate', 'theme-corporate');
extendCapellPackageTests(PackagesTestCase::class, 'theme-saas', 'theme-saas');
pest()->extend(UninstalledPackagesTestCase::class)->in('UninstalledPackages');
extendCapellPackageTests(WordPressImporterTestCase::class, 'wordpress-importer', 'wordpress-importer');
