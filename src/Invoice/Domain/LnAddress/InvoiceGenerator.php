<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\LnAddress;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Shared\Transfer\BackendInvoiceResponse;
use PhpLightning\Shared\Value\SendableRange;

final class InvoiceGenerator
{
    public const MESSAGE_PAYMENT_RECEIVED = 'Payment received!';

    public function __construct(
        private BackendInvoiceInterface $backendInvoice,
        private SendableRange $sendableRange,
        private string $lnAddress,
    ) {
    }

    public function generateInvoice(int $milliSats): array
    {
        if (!$this->sendableRange->contains($milliSats)) {
            return [
                'status' => 'ERROR',
                'reason' => 'Amount is not between minimum and maximum sendable amount',
            ];
        }
        // Modify the description if you want to custom it
        // This will be the description on the wallet that pays your ln address
        // TODO: Make this customizable from some external configuration file
        $description = 'Pay to ' . $this->lnAddress;

        // TODO: images not implemented yet
        $imageMetadata = '';
        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $this->lnAddress . '"]' . $imageMetadata . ']';

        $invoice = $this->backendInvoice->requestInvoice((int)($milliSats / 1000), $metadata);

        return $this->mapResponseAsArray($invoice);
    }

    private function mapResponseAsArray(BackendInvoiceResponse $invoice): array
    {
        return [
            'pr' => $invoice->getPaymentRequest(),
            'status' => $invoice->getStatus(),
            'successAction' => [
                'tag' => 'message',
                'message' => self::MESSAGE_PAYMENT_RECEIVED,
            ],
            'routes' => [],
            'disposable' => false,
            'reason' => $invoice->getReason(),
        ];
    }
}
