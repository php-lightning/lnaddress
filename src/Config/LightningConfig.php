<?php

declare(strict_types=1);

namespace PhpLightning\Config;

use JsonSerializable;
use PhpLightning\Config\Backend\BackendConfigInterface;
use PhpLightning\Config\Backend\BackendType;
use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Shared\Config\ConfigKey;
use PhpLightning\Shared\Value\SendableRange;
use RuntimeException;

use function is_array;
use function sprintf;

final class LightningConfig implements JsonSerializable
{
    private ?BackendsConfig $backends = null;
    private ?string $domain = null;
    private ?string $receiver = null;
    private ?SendableRange $sendableRange = null;
    private ?string $callbackUrl = null;
    private ?string $descriptionTemplate = null;
    private ?string $successMessage = null;
    private ?string $invoiceMemo = null;

    public function setDomain(string $domain): self
    {
        $parseUrl = parse_url($domain);
        $this->domain = $parseUrl['host'] ?? $domain;
        return $this;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function setSendableRange(int $min, int $max): self
    {
        $this->sendableRange = SendableRange::withMinMax($min, $max);
        return $this;
    }

    public function setCallbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function setDescriptionTemplate(string $template): self
    {
        $this->descriptionTemplate = $template;
        return $this;
    }

    public function setSuccessMessage(string $message): self
    {
        $this->successMessage = $message;
        return $this;
    }

    public function setInvoiceMemo(string $memo): self
    {
        $this->invoiceMemo = $memo;
        return $this;
    }

    public function addBackend(string $username, BackendConfigInterface $backendConfig): self
    {
        $this->backends ??= new BackendsConfig();
        $this->backends->add($username, $backendConfig);
        return $this;
    }

    public function addBackendsFile(string $path): self
    {
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Backends file not found: "%s"', $path));
        }

        /** @var mixed $json */
        $json = json_decode((string)file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($json)) {
            throw new RuntimeException(sprintf('Backends file "%s" must contain a JSON object', $path));
        }

        /** @var array<array-key, array{type?: string, api_endpoint?: string, api_key?: string}> $json */
        foreach ($json as $username => $settings) {
            // A numeric username in JSON arrives as an int array key; cast so strict_types holds.
            $this->addBackend((string)$username, $this->createBackendConfig((string)$username, $settings));
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = [];
        if ($this->backends instanceof BackendsConfig) {
            $result[ConfigKey::BACKENDS] = $this->backends->jsonSerialize();
        }
        if ($this->domain !== null) {
            $result[ConfigKey::DOMAIN] = $this->domain;
        }
        if ($this->receiver !== null) {
            $result[ConfigKey::RECEIVER] = $this->receiver;
        }
        if ($this->sendableRange instanceof SendableRange) {
            $result[ConfigKey::SENDABLE_RANGE] = $this->sendableRange;
        }
        if ($this->callbackUrl !== null) {
            $result[ConfigKey::CALLBACK_URL] = $this->callbackUrl;
        }
        if ($this->descriptionTemplate !== null) {
            $result[ConfigKey::DESCRIPTION_TEMPLATE] = $this->descriptionTemplate;
        }
        if ($this->successMessage !== null) {
            $result[ConfigKey::SUCCESS_MESSAGE] = $this->successMessage;
        }
        if ($this->invoiceMemo !== null) {
            $result[ConfigKey::INVOICE_MEMO] = $this->invoiceMemo;
        }

        return $result;
    }

    /**
     * @param array{type?: string, api_endpoint?: string, api_key?: string} $settings
     */
    private function createBackendConfig(string $username, array $settings): BackendConfigInterface
    {
        if (!isset($settings['type'])) {
            throw new RuntimeException(sprintf('Missing "type" for backend "%s"', $username));
        }

        return match (BackendType::fromString($settings['type'])) {
            BackendType::Lnbits => LnBitsBackendConfig::withEndpointAndKey(
                $settings['api_endpoint'] ?? '',
                $settings['api_key'] ?? '',
            ),
        };
    }
}
