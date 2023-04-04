<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;

final class LnbitsBackendInvoice implements BackendInvoiceInterface
{
    /**
     * @param array{api_key:string, api_endpoint:string} $options
     */
    public function __construct(
        private HttpApiInterface $httpApi,
        private array $options,
    ) {
    }

    /**
     * @return array {
     *   status: string,
     *   reason: string,
     * }
     */
    public function requestInvoice(float $satsAmount, string $metadata): array
    {
        $endpoint = $this->options['api_endpoint'] . '/api/v1/payments';

        $content = [
            'out' => false,
            'amount' => $satsAmount,
            'unhashed_description' => bin2hex($metadata),
            // 'description_hash' => hash('sha256', $metadata),
        ];

        $response = $this->httpApi->postRequestInvoice(
            $endpoint,
            body: json_encode($content, JSON_THROW_ON_ERROR),
            headers: [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->options['api_key'],
            ],
        );

        if ($response !== null) {
            return [
                'status' => 'OK',
                'pr' => $response['payment_request'] ?? 'No payment_request found',
            ];
        }

        return [
            'status' => 'ERROR',
            'reason' => 'Backend "LnBits" unreachable',
        ];
    }
}
