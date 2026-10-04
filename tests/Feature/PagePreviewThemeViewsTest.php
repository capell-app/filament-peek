<?php

declare(strict_types=1);

use Capell\Core\Enums\FrontendRuntime;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Theme;
use Capell\Core\Support\PackageRegistry\CapellPackageRegistry;
use Capell\FilamentPeek\Actions\CreatePagePreviewSnapshotAction;
use Capell\FilamentPeek\Actions\RenderPagePreviewSnapshotAction;
use Capell\FilamentPeek\Tests\Fixtures\ThrowingThemeViewFinder;
use Capell\Frontend\Contracts\FrontendResponseRenderer;
use Capell\Frontend\Data\FrontendRenderContextData;
use Capell\Frontend\Support\Render\FrontendResponseRendererRegistry;
use Capell\Frontend\Support\View\ThemeChainResolver;
use Capell\Frontend\Support\View\ThemeViewRegistrar;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\View\FileViewFinder;
use Symfony\Component\HttpFoundation\Response;

function renderPeekRegisteredFixtureView(string $viewName): string
{
    return view()->file(view()->getFinder()->find($viewName))->render();
}

it('preserves package view paths during previews and restores them after success or exceptions', function (string $failure): void {
    $this->actingAs($this->createUserWithRole('super_admin'));
    $language = Language::factory()->create();
    $theme = Theme::factory()->create(['key' => 'preview-test-theme']);
    $site = Site::factory()->withTranslations($language)->language($language)->create();
    $layout = Layout::factory()->site($site)->default()->create(['containers' => [], 'theme_id' => $theme->getKey()]);
    $page = Page::factory()->site($site)->layout($layout)->withTranslations($language)->create();
    $snapshot = CreatePagePreviewSnapshotAction::run($page, ['name' => 'Preview'])['snapshot'];
    app()->instance(ThemeChainResolver::class, new ThemeChainResolver(
        resolve(CapellPackageRegistry::class),
        __DIR__ . '/../Fixtures/theme-chain.php',
    ));

    $finder = view()->getFinder();
    expect($finder)->toBeInstanceOf(FileViewFinder::class);
    assert($finder instanceof FileViewFinder);
    if ($failure === 'registration') {
        $throwingFinder = new ThrowingThemeViewFinder($finder->getFilesystem(), $finder->getPaths());
        foreach ($finder->getHints() as $namespace => $paths) {
            $throwingFinder->addNamespace($namespace, $paths);
        }

        $finder = $throwingFinder;
        view()->setFinder($finder);
        app()->instance('view.finder', $finder);
    }

    $finder->addNamespace('capell', __DIR__ . '/../Fixtures/views');
    $originalPaths = $finder->getHints()['capell'];
    // Bind registration to the real rendering finder, including in isolation.
    app()->instance(ThemeViewRegistrar::class, new ThemeViewRegistrar($finder));

    resolve(FrontendResponseRendererRegistry::class)->register(new readonly class($failure) implements FrontendResponseRenderer
    {
        public function __construct(private string $failure) {}

        #[Override]
        public function runtime(): FrontendRuntime
        {
            return FrontendRuntime::Blade;
        }

        #[Override]
        public function render(FrontendRenderContextData $context): Response|Responsable
        {
            if ($this->failure === 'deferred') {
                return new class implements Responsable
                {
                    #[Override]
                    public function toResponse(mixed $request): Response
                    {
                        return new Response(renderPeekRegisteredFixtureView('capell::preview-package-marker') . renderPeekRegisteredFixtureView('capell::preview-theme-marker'));
                    }
                };
            }

            $html = renderPeekRegisteredFixtureView('capell::preview-package-marker') . renderPeekRegisteredFixtureView('capell::preview-theme-marker');

            if ($this->failure === 'render') {
                throw new RuntimeException('Preview rendering failed after resolving a package view.');
            }

            return new Response($html);
        }
    });

    if (in_array($failure, ['none', 'deferred'], true)) {
        $response = RenderPagePreviewSnapshotAction::run($snapshot);
        expect($response->getContent())->toContain('Registered package view remains available.', 'The preview theme remains available.');
    } else {
        expect(fn (): Response => RenderPagePreviewSnapshotAction::run($snapshot))
            ->toThrow(RuntimeException::class, $failure === 'registration'
                ? 'Theme namespace registration failed after replacement.'
                : 'Preview rendering failed after resolving a package view.');
    }

    expect($finder->getHints()['capell'])->toBe($originalPaths)
        ->and(renderPeekRegisteredFixtureView('capell::preview-package-marker'))->toContain('Registered package view remains available.');
    expect(fn (): string => $finder->find('capell::preview-theme-marker'))
        ->toThrow(InvalidArgumentException::class, 'View [preview-theme-marker] not found.');
})->with(['success' => 'none', 'deferred response' => 'deferred', 'render exception' => 'render', 'registration exception' => 'registration']);
