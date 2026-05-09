<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Models\Registration;
use Capell\AccessGate\Support\RegistrationFieldRegistry;
use Capell\PublicActions\Contracts\PublicActionHandler;
use Capell\PublicActions\Data\PublicActionResultData;
use Capell\PublicActions\Data\PublicActionSubmissionData;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class SubmitAccessGatePublicAction implements PublicActionHandler
{
    public function __construct(
        private readonly CreateRegistrationAction $createRegistration,
        private readonly RegistrationFieldRegistry $fields,
    ) {}

    /**
     * @throws ValidationException
     */
    public function handle(PublicActionSubmissionData $submission): PublicActionResultData
    {
        $payload = $submission->payload->values;
        $validated = Validator::make($payload, [
            'area' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'requested_url' => ['nullable', 'url', 'max:2048'],
            'requested_host' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $registration = $this->createRegistration->handle((string) $validated['area'], [
            ...Arr::except($payload, ['area', 'user_id']),
            'metadata' => [
                ...Arr::wrap($payload['metadata'] ?? []),
                ...$this->metadata($submission),
            ],
        ]);

        return new PublicActionResultData(
            success: true,
            message: __('capell-access-gate::public.request_submitted'),
            createdModelType: Registration::class,
            createdModelId: (string) $registration->getKey(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function metadata(PublicActionSubmissionData $submission): array
    {
        $metadata = array_filter([
            'ip_hash' => $submission->metadata->ipHash,
            'user_agent' => $submission->metadata->userAgent,
            'url' => $submission->metadata->url,
            'referer' => $submission->metadata->referer,
            'route' => $submission->metadata->route,
            'site_id' => $submission->metadata->siteId,
        ], static fn (mixed $value): bool => $value !== null);

        if ($submission->sourceType !== null) {
            $metadata['source_type'] = $submission->sourceType;
        }

        if ($submission->sourceId !== null) {
            $metadata['source_id'] = $submission->sourceId;
        }

        $fieldKeys = array_keys($this->fields->all());

        if ($fieldKeys !== []) {
            $metadata['field_keys'] = $fieldKeys;
        }

        return $metadata;
    }
}
