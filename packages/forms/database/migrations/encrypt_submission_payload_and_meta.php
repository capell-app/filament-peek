<?php

declare(strict_types=1);

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->longText('payload')->nullable()->change();
            $table->longText('meta')->nullable()->change();
        });

        DB::table('submissions')
            ->select(['id', 'payload', 'meta'])
            ->orderBy('id')
            ->chunkById(100, function (Collection $submissions): void {
                foreach ($submissions as $submission) {
                    DB::table('submissions')
                        ->where('id', $submission->id)
                        ->update([
                            'payload' => $this->encryptColumnValue($submission->payload),
                            'meta' => $this->encryptColumnValue($submission->meta),
                        ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('submissions')
            ->select(['id', 'payload', 'meta'])
            ->orderBy('id')
            ->chunkById(100, function (Collection $submissions): void {
                foreach ($submissions as $submission) {
                    DB::table('submissions')
                        ->where('id', $submission->id)
                        ->update([
                            'payload' => $this->decryptColumnValue($submission->payload),
                            'meta' => $this->decryptColumnValue($submission->meta),
                        ]);
                }
            });

        Schema::table('submissions', function (Blueprint $table): void {
            $table->json('payload')->nullable()->change();
            $table->json('meta')->nullable()->change();
        });
    }

    private function encryptColumnValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $storedValue = $this->normalizeStoredValue($value);

        try {
            Crypt::decryptString($storedValue);

            return $storedValue;
        } catch (DecryptException) {
            return Crypt::encryptString($storedValue);
        }
    }

    private function decryptColumnValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $storedValue = $this->normalizeStoredValue($value);

        try {
            return Crypt::decryptString($storedValue);
        } catch (DecryptException) {
            return $storedValue;
        }
    }

    private function normalizeStoredValue(mixed $value): string
    {
        if (! is_string($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        $decodedValue = json_decode($value, associative: true);

        if (is_string($decodedValue)) {
            return $decodedValue;
        }

        return $value;
    }
};
