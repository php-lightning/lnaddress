<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Application;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Shared\Transfer\BackendInvoiceResponse;
use PhpLightning\Shared\Value\SendableRange;

use function sprintf;

final readonly class InvoiceGenerator
{
    public function __construct(
        private BackendInvoiceInterface $backendInvoice,
        private SendableRange $sendableRange,
        private string $lnAddress,
        private string $descriptionTemplate,
        private string $successMessage,
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
        $description = sprintf($this->descriptionTemplate, $this->lnAddress);

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
                'message' => $this->successMessage,
            ],
            'routes' => [],
            'disposable' => false,
            'reason' => $invoice->getReason(),
        ];
    }
}
