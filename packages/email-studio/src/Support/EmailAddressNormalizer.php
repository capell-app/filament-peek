<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Support;

class EmailAddressNormalizer
{
    public function normalize(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public function hash(string $email): string
    {
        return hash('sha256', $this->normalize($email));
    }
}
