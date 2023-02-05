<?php

declare(strict_types=1);

namespace PhpLightning;

use PhpLightning\Invoice\LnBitsInvoice;

final class LnAddress
{
    public const MESSAGE_PAYMENT_RECEIVED = 'Payment received!';

    /** @var int 100 Minimum in msat (sat/1000) */
    public const MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    public const MAX_SENDABLE = 10_000_000_000;

    private const BACKEND_LNBITS = 'lnbits';

    private const TAG_PAY_REQUEST = 'payRequest';

    private HttpApiInterface $httpApi;
    private ConfigInterface $config;

    public function __construct(HttpApiInterface $httpApi, ConfigInterface $config)
    {
        $this->httpApi = $httpApi;
        $this->config = $config;
    }

    /**
     * @param string $image_file The picture you want to display, if you don't want to show a picture, leave an empty string.
     *                          Beware that a heavy picture will make the wallet fails to execute lightning address process! 136536 bytes maximum for base64 encoded picture data
     */
    public function generateInvoice(
        int $amount,
        string $backend = self::BACKEND_LNBITS,
        string $image_file = '',
    ): array {
        // automatically define the ln address based on filename & host, this shouldn't be changed
        $username = str_replace('.php', '', basename(__FILE__));
        $lnAddress = $username . '@' . $this->config->getHttpHost();

        // Modify the description if you want to custom it
        // This will be the description on the wallet that pays your ln address
        $description = 'Pay to ' . $lnAddress;

        $imageMetadata = $this->generateImageMetadata($image_file);
        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $lnAddress . '"]' . $imageMetadata . ']';

        // payRequest json data, spec : https://github.com/lnurl/luds/blob/luds/06.md
        $data = [
            'callback' => $this->callback(),
            'maxSendable' => self::MAX_SENDABLE,
            'minSendable' => self::MIN_SENDABLE,
            'metadata' => $metadata,
            'tag' => self::TAG_PAY_REQUEST,
            'commentAllowed' => 0, // TODO: Not implemented yet
        ];

        if ($amount === 0) {
            return $data;
        }

        if (!$this->isValidAmount($amount)) {
            return [
                'status' => 'ERROR',
                'reason' => 'Amount is not between minimum and maximum sendable amount',
            ];
        }

        $invoice = $this->requestInvoice(
            $backend,
            $amount / 1000,
            $metadata,
        );

        if ($invoice['status'] === 'OK') {
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

        return [
            'status' => $invoice['status'],
            'reason' => $invoice['reason'],
        ];
    }

    private function requestInvoice(string $backend, float $amount, string $metadata): array
    {
        if ($backend === self::BACKEND_LNBITS) {
            $lnbits = new LnBitsInvoice(
                $this->httpApi,
                $this->config->getBackendOptionsFor($backend),
            );

            return $lnbits->requestInvoice($amount, $metadata);
        }

        return [
            'status' => 'ERROR',
            'reason' => 'Unknown Backend: ' . $backend,
        ];
    }

    private function callback(): string
    {
        return 'https://' . $this->config->getHttpHost() . $this->config->getRequestUri();
    }

    private function generateImageMetadata(string $imageFile): string
    {
        if ($imageFile === '') {
            return '';
        }
        $response = $this->httpApi->get($imageFile);
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
}
