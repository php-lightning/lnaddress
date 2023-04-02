<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

use PhpLightning\Http\HttpFacadeInterface;

final class CallbackUrl implements CallbackUrlInterface
{
    /** @var int 100 Minimum in msat (sat/1000) */
    public const MIN_SENDABLE = 100_000;

    /** @var int 10 000 000 Max in msat (sat/1000) */
    public const MAX_SENDABLE = 10_000_000_000;

    private const TAG_PAY_REQUEST = 'payRequest';

    public function __construct(
        private HttpFacadeInterface $httpFacade,
        private string $lnAddress,
        private string $callback,
    ) {
    }

    public function getCallbackUrl(string $imageFile = ''): array
    {
        // Modify the description if you want to custom it
        // This will be the description on the wallet that pays your ln address
        // TODO: Make this customizable from some external configuration file
        $description = 'Pay to ' . $this->lnAddress;

        $imageMetadata = $this->generateImageMetadata($imageFile);
        $metadata = '[["text/plain","' . $description . '"],["text/identifier","' . $this->lnAddress . '"]' . $imageMetadata . ']';

        // payRequest json data, spec : https://github.com/lnurl/luds/blob/luds/06.md
        return [
            'callback' => $this->callback,
            'maxSendable' => self::MAX_SENDABLE,
            'minSendable' => self::MIN_SENDABLE,
            'metadata' => $metadata,
            'tag' => self::TAG_PAY_REQUEST,
            'commentAllowed' => false, // TODO: Not implemented yet
        ];
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
}
