<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Controller;

use Gacela\Framework\ServiceResolverAwareTrait;
use Gacela\Router\Entities\JsonResponse;
use Gacela\Router\Entities\Request;
use PhpLightning\Invoice\InvoiceFacade;

/**
 * @method InvoiceFacade getFacade()
 */
final class InvoiceController
{
    use ServiceResolverAwareTrait;

    public function __construct(
        private Request $request,
    ) {
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function __invoke(string $username = ''): JsonResponse
    {
        // Errors bubble to InvoiceExceptionHandler (registered in InvoiceRoutesPlugin).
        $amount = (int)$this->request->get('amount');

        if ($amount === 0) {
            return new JsonResponse(
                $this->getFacade()->getCallbackUrl($username),
            );
        }

        return new JsonResponse(
            $this->getFacade()->generateInvoice($username, $amount),
        );
    }
}
