<?php

namespace FDevs\ContactListBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FDevsContactListExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['db']) && $config['db'] !== 'none') {
            $container->setParameter($this->getAlias().'.model_manager_name', $config['db']['manager_name']);
            $container->setParameter($this->getAlias().'.backend_type_'.$config['db']['driver'], true);
            $loader->load($config['db']['driver'].'.xml');
            $config['providers'][] = $this->getAlias().'.contact_provider.doctrine';
        }
        $container->setParameter($this->getAlias().'.twig.tpl', $config['tpl']);
        $container->setParameter($this->getAlias().'.model_contact.class', $config['model_contact']);
        $container->setParameter($this->getAlias().'.model_connect.class', $config['model_connect']);
        $container->setParameter($this->getAlias().'.model_address.class', $config['model_address']);

        $loader->load('services.xml');
        $loader->load('form.xml');

        foreach ($config['providers'] as $provider) {
            $container->getDefinition($provider)->addTag('f_devs_contact_list.provider');
        }

        if (isset($config['admin_service']) && $config['admin_service'] !== 'none') {
            $loader->load('admin/'.$config['admin_service'].'.xml');
        }
    }
}
