<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\LnAddress;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\Transfer\SendableRange;

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

        $invoice = $this->backendInvoice->requestInvoice($milliSats / 1000, $metadata);

        if ($invoice['status'] === 'OK') {
            return $this->okResponse($invoice);
        }

        return $this->errorResponse($invoice);
    }

    private function okResponse(array $invoice): array
    {
        return [
            'pr' => $invoice['pr'],
            'status' => 'OK',
            'successAction' => [
                'tag' => 'message',
                'message' => self::MESSAGE_PAYMENT_RECEIVED,
            ],
            'routes' => [],
            'disposable' => false,
        ];
    }

    private function errorResponse(array $invoice): array
    {
        return [
            'status' => $invoice['status'],
            'reason' => $invoice['reason'],
        ];
    }
}
