<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Actions;

use Illuminate\Database\Eloquent\Model;
use JsonException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ComputeDocumentContentHashAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>|string|Model  $content
     *
     * @throws JsonException
     */
    public function handle(array|string|Model $content): string
    {
        $payload = $content instanceof Model
            ? $content->getAttributes()
            : $content;

        if (is_string($payload)) {
            return hash('sha256', $payload);
        }

        return hash('sha256', json_encode(
            $this->normalise($payload),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ));
    }

    private function normalise(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! array_is_list($value)) {
            ksort($value);
        }

        return array_map($this->normalise(...), $value);
    }
}
