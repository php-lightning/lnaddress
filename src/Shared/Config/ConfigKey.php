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
    public const BACKENDS = 'backends';
    public const DOMAIN = 'domain';
    public const RECEIVER = 'receiver';
    public const SENDABLE_RANGE = 'sendable-range';
    public const CALLBACK_URL = 'callback-url';
    public const DESCRIPTION_TEMPLATE = 'description-template';
    public const SUCCESS_MESSAGE = 'success-message';
    public const INVOICE_MEMO = 'invoice-memo';
}
