<?php

namespace FDevs\ContactListBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('f_devs_contact_list');

        $rootNode
            ->children()
                ->append($this->adminService())
                ->append($this->dbDriver())
                ->scalarNode('tpl')->defaultValue('fdevs_contact.html.twig')->end()
                ->scalarNode('model_contact')->defaultValue('FDevs\ContactList\Model\Contact')->end()
                ->scalarNode('model_connect')->defaultValue('FDevs\ContactList\Model\Connect')->end()
                ->scalarNode('model_address')->defaultValue('FDevs\ContactList\Model\Address')->end()
                ->arrayNode('providers')
                    ->defaultValue(['f_devs_contact_list.contact_provider.builder_alias'])
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * add admin config service.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function adminService()
    {
        $supportedAdminService = ['sonata', 'none'];
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('admin_service', 'scalar');

        $rootNode
            ->validate()
            ->ifNotInArray($supportedAdminService)
            ->thenInvalid('The admin service %s is not supported. Please choose one of '.json_encode($supportedAdminService))
            ->end();

        return $rootNode;
    }

    /**
     * add db config.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function dbDriver()
    {
        $supportedDrivers = ['mongodb', 'none'];
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('db');

        $rootNode
//            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')
                  ->defaultValue('mongodb')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                ->end()
                ->scalarNode('manager_name')->defaultNull()->end()
            ->end()
        ;

        return $rootNode;
    }
}
