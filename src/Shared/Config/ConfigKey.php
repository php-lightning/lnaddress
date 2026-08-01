<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Config;

/**
 * Single source of truth for the app-config keys shared between the
 * {@see \PhpLightning\Config\LightningConfig} writer and the
 * {@see \PhpLightning\Invoice\InvoiceConfig} reader, so they cannot drift apart.
 */
final class ConfigKey
{
    public const string BACKENDS = 'backends';
    public const string DOMAIN = 'domain';
    public const string RECEIVER = 'receiver';
    public const string SENDABLE_RANGE = 'sendable-range';
    public const string CALLBACK_URL = 'callback-url';
    public const string DESCRIPTION_TEMPLATE = 'description-template';
    public const string SUCCESS_MESSAGE = 'success-message';
    public const string INVOICE_MEMO = 'invoice-memo';
}
