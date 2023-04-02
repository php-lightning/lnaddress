<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

use PhpLightning\Http\HttpFacadeInterface;
use PhpLightning\Invoice\Domain\Transfer\SendableRange;

final class CallbackUrl implements CallbackUrlInterface
{
    private const TAG_PAY_REQUEST = 'payRequest';

    public function __construct(
        private HttpFacadeInterface $httpFacade,
        private SendableRange $sendableRange,
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
            'maxSendable' => $this->sendableRange->max(),
            'minSendable' => $this->sendableRange->min(),
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
