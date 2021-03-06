<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\EventDispatcher\Listener\AddListenerAwareInterface;
use Arp\EventDispatcher\Listener\AggregateListenerInterface;
use Arp\EventDispatcher\Listener\Exception\EventListenerException;
use Arp\EventDispatcher\Listener\ListenerProvider;
use Arp\EventDispatcher\Resolver\EventNameResolver;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Arp\LaminasFactory\Exception\ServiceNotFoundException;
use Interop\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
class ListenerProviderFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private string $defaultClassName = ListenerProvider::class;

    /**
     * @var array
     */
    protected array $defaultListenerConfig = [];

    /**
     * @var array
     */
    protected array $defaultAggregateListenerConfig = [];

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ListenerProviderInterface
     *
     * @throws ServiceNotCreatedException If the listener provider cannot be created
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): ListenerProviderInterface {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $className = $options['class_name'] ?? $this->defaultClassName;
        if (! is_a($className, ListenerProviderInterface::class, true)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'The \'class_name\' option must be a class of type \'%s\'; \'%s\' provided in \'%s\'',
                    ListenerProviderInterface::class,
                    $className,
                    static::class
                )
            );
        }

        $eventNameResolver = $options['event_name_resolver'] ?? EventNameResolver::class;
        if (is_string($eventNameResolver)) {
            $eventNameResolver = $this->getService($container, $eventNameResolver, $requestedName);
        }

        /** @var ListenerProviderInterface $listenerProvider */
        $listenerProvider = new $className($eventNameResolver);

        if ($listenerProvider instanceof AddListenerAwareInterface) {
            try {
                $listenerConfig = array_replace_recursive($this->defaultListenerConfig, $options['listeners'] ?? []);

                if (! empty($listenerConfig)) {
                    $this->registerCallableListeners($container, $listenerProvider, $listenerConfig, $requestedName);
                }

                $listenerConfig = array_replace_recursive(
                    $this->defaultAggregateListenerConfig,
                    $options['aggregate_listeners'] ?? []
                );

                if (! empty($listenerConfig)) {
                    $this->registerAggregateListeners($container, $listenerProvider, $listenerConfig, $requestedName);
                }
            } catch (EventListenerException $e) {
                throw new ServiceNotCreatedException(
                    sprintf('Failed to register event listeners: %s', $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
        }

        return $listenerProvider;
    }

    /**
     * @param ContainerInterface        $container
     * @param AddListenerAwareInterface $listenerProvider
     * @param callable[][]              $listenerConfig
     * @param string                    $requestedName
     *
     * @throws EventListenerException
     * @throws ServiceNotCreatedException
     */
    private function registerCallableListeners(
        ContainerInterface $container,
        AddListenerAwareInterface $listenerProvider,
        array $listenerConfig,
        string $requestedName
    ): void {
        foreach ($listenerConfig as $eventName => $eventPriorities) {
            foreach ($eventPriorities as $priority => $eventListeners) {
                foreach ($eventListeners as $index => $eventListener) {
                    if (is_string($eventListener)) {
                        $eventListener = $this->getService($container, $eventListener, $requestedName);
                    }

                    if (!is_callable($eventListener)) {
                        throw new ServiceNotCreatedException(
                            sprintf(
                                'The event listener registered for event \'%s\' at index \'%d\'' .
                                'at priority \'%d\' cannot be resolved for \'%s\'',
                                $index,
                                $eventName,
                                $priority,
                                $requestedName
                            )
                        );
                    }
                    $listenerProvider->addListenerForEvent($eventName, $eventListener, $priority);
                }
            }
        }
    }

    /**
     * @param ContainerInterface        $container
     * @param AddListenerAwareInterface $listenerProvider
     * @param array                     $listenerConfig
     * @param string                    $requestedName
     *
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     */
    public function registerAggregateListeners(
        ContainerInterface $container,
        AddListenerAwareInterface $listenerProvider,
        array $listenerConfig,
        string $requestedName
    ): void {
        foreach ($listenerConfig as $aggregateListener) {
            if (is_string($aggregateListener)) {
                $aggregateListener = $this->getService($container, $aggregateListener, $requestedName);
            }

            if (!$aggregateListener instanceof AggregateListenerInterface) {
                throw new ServiceNotCreatedException(
                    sprintf(
                        'The aggregate listener must be an object of type \'%s\'; \'%s\' provided for \'%s\'',
                        AggregateListenerInterface::class,
                        is_object($aggregateListener) ? get_class($aggregateListener) : gettype($aggregateListener),
                        $requestedName
                    )
                );
            }
            $aggregateListener->addListeners($listenerProvider);
        }
    }
}
