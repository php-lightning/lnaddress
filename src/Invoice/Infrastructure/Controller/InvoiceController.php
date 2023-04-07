<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Controller;

use Gacela\Framework\DocBlockResolverAwareTrait;
use PhpLightning\Invoice\InvoiceFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @method InvoiceFacade getFacade()
 */
final class InvoiceController
{
    use DocBlockResolverAwareTrait;

    /**
     * @psalm-suppress InternalMethod
     */
    public function __invoke(Request $request): Response
    {
        $username = (string)$request->get('username');
        $milliSats = (int)$request->get('amount', 0);
        //        TODO: Make it customizable
        //        $backend = (string)$request->get('backend', 'lnbits');

        try {
            if ($milliSats === 0) {
                return new JsonResponse(
                    $this->getFacade()->getCallbackUrl($username),
                );
            }

            return new JsonResponse(
                $this->getFacade()->generateInvoice($username, $milliSats),
            );
        } catch (Throwable $e) {
            dd($e); # temporal for debugging purposes...
        }
    }
}
