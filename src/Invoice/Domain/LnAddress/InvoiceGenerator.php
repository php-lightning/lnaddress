<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\LnAddress;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\BackendInvoice\BackendInvoiceInterface;
use PhpLightning\Invoice\Domain\Transfer\SendableRange;

final class InvoiceGenerator
{
    public const MESSAGE_PAYMENT_RECEIVED = 'Payment received!';

    public function __construct(
        private HttpFacadeInterface $httpFacade,
        private BackendInvoiceInterface $backendInvoice,
        private SendableRange $sendableRange,
        private string $lnAddress,
    ) {
    }

    /**
     * @param string $imageFile The picture you want to display, if you don't want to show a picture, leave an empty string.
     *                          Beware that a heavy picture will make the wallet fails to execute lightning address process! 136536 bytes maximum for base64 encoded picture data
     */
    public function generateInvoice(int $amount, string $imageFile = ''): array
    {
        if (!$this->sendableRange->contains($amount)) {
            return [
                'status' => 'ERROR',
                'reason' => 'Amount is not between minimum and maximum sendable amount',
            ];
        }
        // Modify the description if you want to custom it
        // This will be the description on the wallet that pays your ln address
        // TODO: Make this customizable from some external configuration file
        $description = 'Pay to ' . $this->lnAddress;

        $imageMetadata = $this->generateImageMetadata($imageFile);
        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $this->lnAddress . '"]' . $imageMetadata . ']';

        $invoice = $this->backendInvoice->requestInvoice($amount / 1000, $metadata);

        if ($invoice['status'] === 'OK') {
            return $this->okResponse($invoice);
        }

        return $this->errorResponse($invoice);
    }

    private function generateImageMetadata(string $imageFile): string
    {
        if ($imageFile === '') {
            return '';
        }
        $response = $this->httpFacade->get($imageFile);
        if ($response === null) {
            return '';
        }

        return ',["image/jpeg;base64","' . base64_encode($response) . '"]';
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
