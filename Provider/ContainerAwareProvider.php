<?php

namespace FDevs\ContactListBundle\Provider;

use FDevs\ContactList\Exception\NotFoundException;
use FDevs\ContactList\Provider\ContactProviderInterface;
use FDevs\ContactList\ContactInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ContainerAwareProvider implements ContactProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * init.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        try {
            $contact = $this->container->get($name);
            if (!$contact instanceof ContactInterface) {
                throw new NotFoundException(sprintf('contact with id "%s" not found', $name));
            }
        } catch (ServiceNotFoundException $e) {
            throw new NotFoundException(sprintf('contact with id "%s" not found', $name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return $this->container->has($name) && $this->container->get($name) instanceof ContactInterface;
    }
}
