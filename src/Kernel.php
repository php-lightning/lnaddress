<?php

declare(strict_types=1);

namespace PhpLightning;

use Gacela\Framework\Bootstrap\GacelaConfig;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function gacelaConfigFn(): callable
    {
        return static function (GacelaConfig $config): void {
            $config->addAppConfig('lightning-config.dist.php', 'lightning-config.php');
            $config->setFileCacheEnabled(true);
        };
    }
}
