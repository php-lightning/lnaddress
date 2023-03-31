<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress\Domain;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\InvoiceFacadeInterface;

final class InvoiceGenerator
{
    public const MESSAGE_PAYMENT_RECEIVED = 'Payment received!';

    /** @var int 100 Minimum in msat (sat/1000) */
    public const MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    public const MAX_SENDABLE = 10_000_000_000;

    private const TAG_PAY_REQUEST = 'payRequest';

    private const DEFAULT_BACKEND = 'lnbits';

    public function __construct(
        private InvoiceFacadeInterface $invoiceFacade,
        private HttpFacadeInterface $httpFacade,
        private LnAddressGeneratorInterface $lnAddressGenerator,
        private string $callback,
    ) {
    }

    /**
     * @param string $imageFile The picture you want to display, if you don't want to show a picture, leave an empty string.
     *                          Beware that a heavy picture will make the wallet fails to execute lightning address process! 136536 bytes maximum for base64 encoded picture data
     */
    public function generateInvoice(
        int $amount,
        string $backend = self::DEFAULT_BACKEND,
        string $imageFile = '',
    ): array {
        $lnAddress = $this->lnAddressGenerator->generateLnAddress();

        // Modify the description if you want to custom it
        // This will be the description on the wallet that pays your ln address
        // TODO: Make this customizable from some external configuration file
        $description = 'Pay to ' . $lnAddress;

        $imageMetadata = $this->generateImageMetadata($imageFile);
        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $lnAddress . '"]' . $imageMetadata . ']';

        if ($amount === 0) {
            // payRequest json data, spec : https://github.com/lnurl/luds/blob/luds/06.md
            return [
                'callback' => $this->callback,
                'maxSendable' => self::MAX_SENDABLE,
                'minSendable' => self::MIN_SENDABLE,
                'metadata' => $metadata,
                'tag' => self::TAG_PAY_REQUEST,
                'commentAllowed' => 0, // TODO: Not implemented yet
            ];
        }

        if (!$this->isValidAmount($amount)) {
            return [
                'status' => 'ERROR',
                'reason' => 'Amount is not between minimum and maximum sendable amount',
            ];
        }

        $invoice = $this->invoiceFacade
            ->requestInvoice($backend, $amount / 1000, $metadata);

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

    private function isValidAmount(int $amount): bool
    {
        return $amount >= self::MIN_SENDABLE
            && $amount <= self::MAX_SENDABLE;
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
