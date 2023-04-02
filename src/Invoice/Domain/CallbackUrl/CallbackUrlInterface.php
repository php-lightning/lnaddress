<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

interface CallbackUrlInterface
{
    /**
     * @return array{
     *     callback: string,
     *     maxSendable: int,
     *     minSendable: int,
     *     metadata: string,
     *     tag: string,
     *     commentAllowed: bool,
     * }
     */
    public function getCallbackUrl(string $imageFile = ''): array;
}
