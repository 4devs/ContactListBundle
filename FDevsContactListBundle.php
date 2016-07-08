<?php

namespace FDevs\ContactListBundle;

use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use FDevs\ContactListBundle\DependencyInjection\Compiler\AddProvidersPass;
use FDevs\ContactListBundle\DependencyInjection\Compiler\AddTemplatePathPass;
use FDevs\ContactListBundle\DependencyInjection\Compiler\AddTranslatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FDevsContactListBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->addRegisterMappingsPass($container);
        $container->addCompilerPass(new AddTemplatePathPass());
        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new AddTranslatorPass());
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $refl = new \ReflectionClass('FDevs\ContactList\ContactFactory');

        $mappings = [realpath(dirname($refl->getFileName()).'/Resources/config/doctrine/model') => 'FDevs\ContactList\Model'];

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(
                DoctrineMongoDBMappingsPass::createXmlMappingDriver(
                    $mappings,
                    ['f_devs_contact_list.model_manager_name'],
                    'f_devs_contact_list.backend_type_mongodb'
                )
            );
        }
    }
}
