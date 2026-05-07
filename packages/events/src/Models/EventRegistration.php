<?php

declare(strict_types=1);

namespace Capell\Events\Models;

use Capell\Events\Database\Factories\EventRegistrationFactory;
use Capell\Events\Enums\EventRegistrationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    /** @use HasFactory<EventRegistrationFactory> */
    use HasFactory;

    protected $table = 'event_registrations';

    protected $guarded = [];

    protected static string $factory = EventRegistrationFactory::class;

    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(EventOccurrence::class, 'event_occurrence_id');
    }

    protected function casts(): array
    {
        return [
            'cancelled_at' => 'immutable_datetime',
            'meta' => 'json',
            'payload' => 'json',
            'quantity' => 'integer',
            'registered_at' => 'immutable_datetime',
            'status' => EventRegistrationStatusEnum::class,
            'waitlist_position' => 'integer',
        ];
    }
}
