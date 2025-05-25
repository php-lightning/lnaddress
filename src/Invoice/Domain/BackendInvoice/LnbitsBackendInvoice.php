<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\BackendInvoice;

use PhpLightning\Invoice\Domain\Http\HttpApiInterface;
use PhpLightning\Shared\Transfer\InvoiceTransfer;

final class LnbitsBackendInvoice implements BackendInvoiceInterface
{
    /**
     * @param  array{api_key:string, api_endpoint:string}  $options
     */
    public function __construct(
        private readonly HttpApiInterface $httpApi,
        private array $options,
    ) {
    }

    public function requestInvoice(int $satsAmount, string $metadata = '', string $memo = ''): InvoiceTransfer
    {
        $endpoint = $this->options['api_endpoint'] . '/api/v1/payments';

        $content = [
            'out' => false,
            'amount' => $satsAmount,
            'memo' => $memo,
            'unhashed_description' => bin2hex($metadata),
            'description_hash' => hash('sha256', $metadata),
        ];

        $response = $this->httpApi->postRequestInvoice(
            $endpoint,
            body: json_encode($content, JSON_THROW_ON_ERROR),
            headers: [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->options['api_key'],
            ],
        );

        if ($response === null) {
            return new InvoiceTransfer(status: 'ERROR', error: 'Backend "LnBits" unreachable');
        }

        return InvoiceTransfer::fromArray($response);
    }
}
