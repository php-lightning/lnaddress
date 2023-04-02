<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Http\HttpFacadeInterface;

use function strlen;

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
        $api_route = '/api/v1/payments';

        $http_body = [
            'out' => false,
            'amount' => $satsAmount,
            'unhashed_description' => bin2hex($metadata),
            // 'description_hash' => hash('sha256', $metadata),
        ];

        $http_req = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Length: ' . strlen((string)json_encode($http_body)) . "\r\n"
                    . "Content-Type: application/json\r\n"
                    . 'X-Api-Key: ' . $this->options['api_key'] . "\r\n",
                'content' => json_encode($http_body),
            ],
        ];

        $req_context = stream_context_create($http_req);
        $response = $this->httpFacade->get($this->options['api_endpoint'] . $api_route, $req_context);

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
