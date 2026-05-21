<?php

declare(strict_types=1);

use Capell\SeoSuite\Support\AbstractThemeSchemaGenerator;

it('hex escapes unsafe characters in JSON-LD output', function (): void {
    $generator = new class extends AbstractThemeSchemaGenerator
    {
        protected function resolveOrgName(): string
        {
            return 'Acme';
        }

        protected function resolveSameAs(): array
        {
            return [];
        }
    };

    $json = $generator->toJsonLd([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => '</script><script>alert("x")</script>',
        'description' => "Tom & O'Connor",
    ]);

    expect($json)
        ->toContain('\u003C/script\u003E')
        ->toContain('\u0026')
        ->toContain('\u0027')
        ->toContain('\u0022')
        ->not->toContain('</script>');
});

it('builds common theme schema payloads with optional organization details', function (): void {
    $generator = new class extends AbstractThemeSchemaGenerator
    {
        protected function resolveOrgName(): string
        {
            return 'Acme Studio';
        }

        protected function resolveSameAs(): array
        {
            return [
                'https://linkedin.test/acme',
                'https://github.test/acme',
            ];
        }

        protected function resolveOrgLogo(): string
        {
            return 'https://example.test/logo.png';
        }

        protected function resolveOrgDescription(): string
        {
            return 'A design and publishing studio.';
        }
    };

    expect($generator->organization('https://example.test'))->toMatchArray([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Acme Studio',
        'url' => 'https://example.test',
        'logo' => 'https://example.test/logo.png',
        'description' => 'A design and publishing studio.',
        'sameAs' => [
            'https://linkedin.test/acme',
            'https://github.test/acme',
        ],
    ]);

    expect($generator->website('https://example.test/', 'Acme Search'))->toMatchArray([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'url' => 'https://example.test/',
        'name' => 'Acme Search',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => 'https://example.test/search?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ]);

    expect($generator->breadcrumb([
        ['name' => 'Home', 'url' => 'https://example.test'],
        ['name' => 'Services', 'url' => 'https://example.test/services'],
    ])['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://example.test'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => 'https://example.test/services'],
    ]);

    expect($generator->faq([
        ['question' => 'What is Capell?', 'answer' => 'A CMS.'],
    ])['mainEntity'])->toBe([
        [
            '@type' => 'Question',
            'name' => 'What is Capell?',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A CMS.'],
        ],
    ]);

    expect($generator->article([
        'headline' => 'Launch notes',
        'description' => 'Release summary',
        'image' => 'https://example.test/image.jpg',
        'datePublished' => '2026-05-01',
        'author' => 'Ben Johnson',
        'url' => 'https://example.test/launch',
    ]))->toMatchArray([
        '@type' => 'Article',
        'headline' => 'Launch notes',
        'dateModified' => '2026-05-01',
        'author' => ['@type' => 'Person', 'name' => 'Ben Johnson'],
    ]);
});
