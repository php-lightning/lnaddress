<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Controller;

use Gacela\Framework\DocBlockResolverAwareTrait;
use Gacela\Router\Entities\JsonResponse;
use Gacela\Router\Entities\Request;
use PhpLightning\Invoice\InvoiceFacade;
use Throwable;

/**
 * @method InvoiceFacade getFacade()
 */
final class InvoiceController
{
    use DocBlockResolverAwareTrait;

    public function __construct(
        private Request $request,
    ) {
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function __invoke(string $username = '', int $amount = 0): JsonResponse
    {
        try {
            if ($amount === 0) {
                $amount = (int)$this->request->get('amount');
            }

            if ($amount === 0) {
                return new JsonResponse(
                    $this->getFacade()->getCallbackUrl($username),
                );
            }

            return new JsonResponse(
                $this->getFacade()->generateInvoice($username, $amount),
            );
        } catch (Throwable $e) {
            return new JsonResponse([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
