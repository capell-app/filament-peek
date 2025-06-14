<?php

declare(strict_types=1);

namespace Capell\Layout\Services\Creator;

use Capell\Core\Enums\TypeEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models;
use Illuminate\Database\Eloquent\Collection;

class ContentCreator
{
    /**
     * @var class-string<Models\Content>
     */
    private readonly string $contentModel;

    /**
     * @var class-string<Models\Type>
     */
    private readonly string $typeModel;

    public function __construct()
    {
        $this->contentModel = CapellCore::getModel('content');
        $this->typeModel = CapellCore::getModel('type');
    }

    public function createContent(array $data, ?Models\Site $site, Collection $languages): Models\Content
    {
        if (! empty($data['type'])) {
            $type = $this->typeModel::contentType()->where('key', $data['type'])->first();
        } else {
            $type = $this->typeModel::contentType()->default()->first();
        }

        $meta = [];

        if (! empty($data['image_id'])) {
            $meta['image_id'] = $data['image_id'];
        }

        $content = $this->contentModel::create([
            'name' => $data['name'],
            'site_id' => $site?->id,
            'type_id' => $type->id,
            'parent_uuid' => $data['parent_uuid'] ?? null,
            'meta' => $meta !== [] ? $meta : null,
        ]);

        foreach ($languages as $language) {
            $translation_data = $data['translations'][$language->code];

            $content->translations()->firstOrCreate([
                'language_id' => $language->id,
            ], [
                'title' => $translation_data['title'],
                'content' => $translation_data['contents'] ?? null,
                'meta' => $translation_data['meta'] ?? [],
            ]);
        }

        return $content;
    }

    public function createContentTypes(): void
    {
        $this->typeModel::firstOrCreate([
            'default' => true,
            'type' => TypeEnum::Content,
        ], [
            'name' => __('capell-admin::generic.default'),
            'key' => 'default',
        ]);
    }
}
