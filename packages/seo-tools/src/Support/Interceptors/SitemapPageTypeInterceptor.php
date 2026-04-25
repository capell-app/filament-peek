<?php

declare(strict_types=1);

namespace Capell\SeoTools\Support\Interceptors;

use Capell\Admin\Filament\Schemas\Types\PageTypeSchema;
use Capell\Core\Contracts\ModelInterceptors\TypeInterceptorInterface;
use Capell\Core\Models\Type;

class SitemapPageTypeInterceptor implements TypeInterceptorInterface
{
    public function beforeCreate(array $data): array
    {
        $data['admin'] = [
            'type_schema' => PageTypeSchema::getKey(),
            'icon' => 'heroicon-o-map',
            'required_fields' => ['title'],
        ];

        return $data;
    }

    public function afterCreated(Type $type, array $data): void {}
}
