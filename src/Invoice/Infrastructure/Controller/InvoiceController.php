<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceController
{
    public function generateAction(Request $request): Response
    {
        return new Response('response :)');
    }
}
