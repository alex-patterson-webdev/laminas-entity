<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Persistence;

use Arp\DoctrineEntityRepository\Persistence\PersistService;
use Arp\DoctrineEntityRepository\Persistence\PersistServiceInterface;
use Arp\EventDispatcher\EventDispatcher;
use Arp\EventDispatcher\Listener\ListenerProvider;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Persistence
 */
final class PersistServiceFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private $defaultClassName = PersistService::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return PersistServiceInterface
     *
     * @throws ServiceNotCreatedException If the persist service cannot be created.
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): PersistServiceInterface {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $className = $options['class_name'] ?? $this->defaultClassName;
        $entityName = $options['entity_name'] ?? null;

        if (null === $entityName || !is_string($entityName)) {
            throw new ServiceNotCreatedException(
                sprintf('The required \'entity_name\' configuration option is missing for service %s', $requestedName)
            );
        }

        $entityManager = $options['entity_manager'] ?? EntityManager::class;
        if (is_string($entityManager)) {
            $entityManager = $this->getService($container, $entityManager, $requestedName);
        }

        $listenerProvider = $options['listener_provider'] ?? ListenerProvider::class;
        if (is_string($listenerProvider)) {
            $listenerProvider = $this->getService($container, $listenerProvider, $requestedName);
        }

        $logger = $options['logger'] ?? NullLogger::class;
        if (is_string($logger)) {
            $logger = $this->getService($container, $logger, $requestedName);
        }

        return new $className(
            $entityName,
            $entityManager,
            new EventDispatcher($listenerProvider),
            $logger
        );
    }
}
