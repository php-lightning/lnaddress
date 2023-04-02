<?php

declare(strict_types=1);

namespace PhpLightning\Config\Backend;

use JsonSerializable;

interface BackendConfigInterface extends JsonSerializable
{
    public function getBackendName(): string;
}
