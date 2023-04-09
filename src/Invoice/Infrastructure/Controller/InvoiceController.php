<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Controller;

use Gacela\Framework\DocBlockResolverAwareTrait;
use Gacela\Router\Request;
use PhpLightning\Invoice\InvoiceFacade;
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
    public function __invoke(string $username = '', int $amount = 0): string
    {
        try {
            if ($amount === 0) {
                $request = Request::fromGlobals();
                $amount = (int)$request->get('amount');
            }

            if ($amount === 0) {
                return $this->json(
                    $this->getFacade()->getCallbackUrl($username),
                );
            }

            return $this->json(
                $this->getFacade()->generateInvoice($username, $amount),
            );
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    private function json(array $json): string
    {
        header('Content-Type: application/json');

        return json_encode($json, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    private function error(Throwable $e): string
    {
        return $this->json([
            'status' => 'ERROR',
            'message' => $e->getMessage(),
        ]);
    }
}
