<?php

declare(strict_types=1);

namespace PhpLightning;

use function strlen;

final class LnAddress
{
    public const MESSAGE_PAYMENT_RECEIVED = 'Payment received!';

    private const DEFAULT_BACKEND = 'lnbits';

    private const TAG_PAY_REQUEST = 'payRequest';

    /** @var int 100 Minimum in msat (sat/1000) */
    private const DEFAULT_MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    private const DEFAULT_MAX_SENDABLE = 10_000_000_000;

    private HttpApiInterface $httpApi;
    private ConfigInterface $config;

    public function __construct(HttpApiInterface $httpApi, ConfigInterface $config)
    {
        $this->httpApi = $httpApi;
        $this->config = $config;
    }

    /**
     * @param array{
     *   lnbits: array{
     *     api_endpoint: string,
     *     api_key?: string,
     *   }
     * } $backendOptions
     * @param string $image_file The picture you want to display, if you don't want to show a picture, leave an empty string.
     *                          Beware that a heavy picture will make the wallet fails to execute lightning address process! 136536 bytes maximum for base64 encoded picture data
     */
    public function generateInvoice(
        int $amount,
        string $backend = self::DEFAULT_BACKEND,
        array $backendOptions = [],
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
            'maxSendable' => self::DEFAULT_MAX_SENDABLE,
            'minSendable' => self::DEFAULT_MIN_SENDABLE,
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
            $backendOptions[$backend] ?? [],
            $amount / 1000,
            $metadata,
            $lnAddress,
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

    private function requestInvoice(
        string $backend,
        array $backend_options,
        float $amount,
        string $metadata,
        string $lnaddr = '',
        bool $comment_allowed = false,
        ?string $comment = null,
    ): array {
        if ($backend === self::DEFAULT_BACKEND) {
            $http_method = 'POST';
            $api_route = '/api/v1/payments';

            $http_body = [
                'out' => false,
                'amount' => $amount,
                'unhashed_description' => bin2hex($metadata),
//                'description_hash' => hash('sha256', $metadata),
            ];

            $http_req = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Length: ' . strlen(json_encode($http_body)) . "\r\n"
                        . "Content-Type: application/json\r\n"
                        . 'X-Api-Key: ' . $backend_options['api_key'] . "\r\n",
                    'content' => json_encode($http_body),
                ],
            ];

            $req_context = stream_context_create($http_req);
            $response = $this->httpApi->get($backend_options['api_endpoint'] . $api_route, $req_context);

            if ($response !== null) {
                $responseJson = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

                return [
                    'status' => 'OK',
                    'pr' => $responseJson['payment_request'],
                ];
            }
        }

        return [
            'status' => 'ERROR',
            'reason' => 'Backend is unreachable',
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

        return ',["image/jpeg;base64","' . base64_encode($this->httpApi->get($imageFile)) . '"]';
    }

    private function isValidAmount(int $amount): bool
    {
        return $amount >= self::DEFAULT_MIN_SENDABLE
            && $amount <= self::DEFAULT_MAX_SENDABLE;
    }
}
