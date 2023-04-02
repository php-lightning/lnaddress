<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Http\HttpFacadeInterface;

final class LnbitsBackendInvoice implements BackendInvoiceInterface
{
    private HttpFacadeInterface $httpFacade;

    /** @var array{api_key:string, api_endpoint:string} */
    private array $options;

    /**
     * @param array{api_key:string, api_endpoint:string} $options
     */
    public function __construct(
        HttpFacadeInterface $httpFacade,
        array $options,
    ) {
        $this->httpFacade = $httpFacade;
        $this->options = $options;
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

        $response = $this->httpFacade->post($endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->options['api_key'],
            ],
            'body' => json_encode($content, JSON_THROW_ON_ERROR),
        ]);

        if ($response !== null) {
            /** @var array{payment_request?: string} $responseJson */
            $responseJson = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            return [
                'status' => 'OK',
                'pr' => $responseJson['payment_request'] ?? 'No payment_request found',
            ];
        }

        return [
            'status' => 'ERROR',
            'reason' => 'Backend "LnBits" unreachable',
        ];
    }
}
