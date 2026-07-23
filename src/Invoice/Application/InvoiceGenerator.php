<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Application;

use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Shared\Transfer\InvoiceTransfer;
use PhpLightning\Shared\Value\LnurlPayMetadata;
use PhpLightning\Shared\Value\SendableRange;

final readonly class InvoiceGenerator
{
    public function __construct(
        private BackendInvoiceInterface $backendInvoice,
        private SendableRange $sendableRange,
        private string $lnAddress,
        private string $descriptionTemplate,
        private string $successMessage,
        private string $memo,
    ) {
    }

    /**
     * @return array{
     *     bolt11: string,
     *     status: string,
     *     memo: string,
     *     successAction: array{tag: string, message: string},
     *     routes: list<mixed>,
     *     disposable: bool,
     *     error: string|null,
     * }|array{status: string, reason: string}
     */
    public function generateInvoice(int $milliSats): array
    {
        if (!$this->sendableRange->contains($milliSats)) {
            return [
                'status' => 'ERROR',
                'reason' => 'Amount is not between minimum and maximum sendable amount',
            ];
        }

        $metadata = new LnurlPayMetadata($this->descriptionTemplate, $this->lnAddress);

        $invoice = $this->backendInvoice->requestInvoice((int)($milliSats / 1000), (string)$metadata, $this->memo);

        return $this->mapResponseAsArray($invoice);
    }

    /**
     * @return array{
     *     bolt11: string,
     *     status: string,
     *     memo: string,
     *     successAction: array{tag: string, message: string},
     *     routes: list<mixed>,
     *     disposable: bool,
     *     error: string|null,
     * }
     */
    private function mapResponseAsArray(InvoiceTransfer $invoice): array
    {
        return [
            'bolt11' => $invoice->bolt11,
            'status' => $invoice->status,
            'memo' => $invoice->memo,
            'successAction' => [
                'tag' => 'message',
                'message' => $this->successMessage,
            ],
            'routes' => [],
            'disposable' => false,
            'error' => $invoice->error,
        ];
    }
}
