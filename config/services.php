<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $configurator) {
    // default configuration for services in *this* file
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // make classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('PhpLightning\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Tests,Kernel.php}');
};
