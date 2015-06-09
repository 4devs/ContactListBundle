<?php

namespace FDevs\ContactListBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTemplatePathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig.loader.filesystem')) {
            return;
        }
        $loaderDefinition = $container->getDefinition('twig.loader.filesystem');

        $refl = new \ReflectionClass('FDevs\ContactList\ContactFactory');
        $path = dirname($refl->getFileName()) . '/Resources/views';
        $loaderDefinition->addMethodCall('addPath', array($path));

    }
}