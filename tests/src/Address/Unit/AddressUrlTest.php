<?php

declare(strict_types=1);

use Capell\Address\Support\Address\AddressUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

it('returns a Google Maps URL for a model address', function (): void {
    $address = new class extends Model
    {
        use HasFactory;

        public $full_address = '123 Main St';

        public $meta = [
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
        ];
    };
    $url = AddressUrl::url($address);
    expect($url)->toBe('https://www.google.com/maps/search/?api=1&query=123+Main+St@40.7128,-74.0060');
});
