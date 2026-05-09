<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Exceptions;

use RuntimeException;

class EmailStudioSendingException extends RuntimeException
{
    public static function profileNotFound(string $siteScopeKey): self
    {
        return new self(sprintf('No email profile is available for site scope [%s].', $siteScopeKey));
    }

    public static function templateNotFound(string $templateKey, string $siteScopeKey): self
    {
        return new self(sprintf('No approved email template [%s] is available for site scope [%s].', $templateKey, $siteScopeKey));
    }

    public static function variantNotFound(string $templateKey, string $siteScopeKey): self
    {
        return new self(sprintf('No active email template variant [%s] is available for site scope [%s].', $templateKey, $siteScopeKey));
    }

    public static function noRecipients(string $templateKey): self
    {
        return new self(sprintf('No recipients were provided for email template [%s].', $templateKey));
    }

    public static function providerNotRegistered(string $provider): self
    {
        return new self(sprintf('No email provider adapter is registered for [%s].', $provider));
    }
}
