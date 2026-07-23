<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Application;

use PhpLightning\Invoice\Domain\CallbackUrl\CallbackUrlInterface;
use PhpLightning\Invoice\Domain\CallbackUrl\LnAddressGeneratorInterface;
use PhpLightning\Shared\Value\LnurlPayMetadata;
use PhpLightning\Shared\Value\SendableRange;

final readonly class CallbackUrl implements CallbackUrlInterface
{
    private const TAG_PAY_REQUEST = 'payRequest';

    public function __construct(
        private SendableRange $sendableRange,
        private LnAddressGeneratorInterface $lnAddressGenerator,
        private string $callback,
        private string $descriptionTemplate,
    ) {
    }

    public function getCallbackUrl(string $username): array
    {
        $lnAddress = $this->lnAddressGenerator->generate($username);
        $metadata = new LnurlPayMetadata($this->descriptionTemplate, $lnAddress);

        // payRequest json data, spec: https://github.com/lnurl/luds/blob/luds/06.md
        return [
            'callback' => $this->callback,
            'maxSendable' => $this->sendableRange->max(),
            'minSendable' => $this->sendableRange->min(),
            'metadata' => (string)$metadata,
            'tag' => self::TAG_PAY_REQUEST,
            'commentAllowed' => false, // TODO: Not implemented yet
        ];
    }
}
