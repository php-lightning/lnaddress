<?php

declare(strict_types=1);

use PhpLightning\Config\LightningConfig;

/** @var LightningConfig $config */
$config = require 'default.php';
$config->setMode('prod');

return $config;
