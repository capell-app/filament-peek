<?php

declare(strict_types=1);

namespace Capell\Newsletter\Actions;

use Capell\Newsletter\Enums\PublicTokenType;
use Capell\Newsletter\Models\PublicToken;
use Capell\Newsletter\Models\Subscriber;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUnsubscribeTokenAction
{
    use AsAction;

    public function handle(Subscriber $subscriber): string
    {
        $rawToken = Str::random(64);

        PublicToken::query()->create([
            'subscriber_id' => $subscriber->getKey(),
            'type' => PublicTokenType::Unsubscribe,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => null,
        ]);

        return $rawToken;
    }
}
