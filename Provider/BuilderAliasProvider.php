<?php

namespace FDevs\ContactListBundle\Provider;


use FDevs\ContactList\ContactInterface;
use FDevs\ContactList\FactoryInterface;
use FDevs\ContactList\Provider\ContactProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class BuilderAliasProvider implements ContactProviderInterface
{
    /** @var KernelInterface */
    private $kernel;

    /** @var ContainerInterface */
    private $container;

    /** @var FactoryInterface */
    private $contactFactory;

    /** @var array */
    private $builders;

    /** @var string */
    private $folder = 'Contact';

    /**
     * init
     *
     * @param KernelInterface    $kernel
     * @param ContainerInterface $container
     * @param FactoryInterface   $contactFactory
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container, FactoryInterface $contactFactory)
    {
        $this->kernel = $kernel;
        $this->container = $container;
        $this->contactFactory = $contactFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Invalid pattern passed to AliasProvider - expected "bundle:class:method", got "%s".', $name));
        }
        list($bundleName, $className, $methodName) = explode(':', $name);
        $builder = $this->getBuilder($bundleName, $className);

        if (!method_exists($builder, $methodName)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" was not found on class "%s" when rendering the "%s" contact.', $methodName, $className, $name));
        }

        $contact = $builder->$methodName($this->contactFactory, $options);
        if (!$contact instanceof ContactInterface) {
            throw new \InvalidArgumentException(sprintf('Method "%s" did not return an ContactInterface contact object for contact "%s"', $methodName, $name));
        }

        return $contact;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        return 2 == substr_count($name, ':');
    }

    /**
     * get Builder
     *
     * @param string $bundleName
     * @param string $className
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getBuilder($bundleName, $className)
    {
        $name = sprintf('%s:%s', $bundleName, $className);

        if (!isset($this->builders[$name])) {
            $class = null;
            $logs = array();
            $bundles = array();

            foreach ($this->kernel->getBundle($bundleName, false) as $bundle) {
                $try = $bundle->getNamespace() . '\\' . $this->folder . '\\' . $className;
                if (class_exists($try)) {
                    $class = $try;
                    break;
                }

                $logs[] = sprintf('Class "%s" does not exist for contact builder "%s".', $try, $name);
                $bundles[] = $bundle->getName();
            }

            if (null === $class) {
                if (1 === count($logs)) {
                    throw new \InvalidArgumentException($logs[0]);
                }

                throw new \InvalidArgumentException(sprintf('Unable to find contact builder "%s" in bundles %s.', $name, implode(', ', $bundles)));
            }

            $builder = new $class();
            if ($builder instanceof ContainerAwareInterface) {
                $builder->setContainer($this->container);
            }

            $this->builders[$name] = $builder;
        }

        return $this->builders[$name];
    }

}
