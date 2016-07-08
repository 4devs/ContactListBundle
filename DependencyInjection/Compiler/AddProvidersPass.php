<?php

namespace FDevs\ContactListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('f_devs_contact_list.contact_provider.chain')) {
            return;
        }

        $providers = array();
        foreach ($container->findTaggedServiceIds('f_devs_contact_list.provider') as $id => $tags) {
            $providers[] = new Reference($id);
        }

        if (1 === count($providers)) {
            $container->setAlias('f_devs_contact_list.contact_provider', (string) reset($providers));
        } else {
            $definition = $container->getDefinition('f_devs_contact_list.contact_provider.chain');
            $definition->replaceArgument(0, $providers);
            $container->setAlias('f_devs_contact_list.contact_provider', 'f_devs_contact_list.contact_provider.chain');
        }
    }
}
