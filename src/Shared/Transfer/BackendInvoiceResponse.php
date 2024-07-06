<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Transfer;

final class BackendInvoiceResponse
{
    private string $status = 'OK';

    private string $paymentRequest = '';

    /** @var list<string> */
    private array $successAction = [];

    /** @var list<string> */
    private array $routes = [];

    private bool $disposable = false;

    private string $reason = '';

    public function getPaymentRequest(): string
    {
        return $this->paymentRequest;
    }

    public function setPaymentRequest(string $paymentRequest): self
    {
        $this->paymentRequest = $paymentRequest;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return list<string>
     */
    public function getSuccessAction(): array
    {
        return $this->successAction;
    }

    /**
     * @param list<string> $successAction
     */
    public function setSuccessAction(array $successAction): self
    {
        $this->successAction = $successAction;
        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param list<string> $routes
     */
    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;
        return $this;
    }

    public function isDisposable(): bool
    {
        return $this->disposable;
    }

    public function setDisposable(bool $disposable): self
    {
        $this->disposable = $disposable;
        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }
}
