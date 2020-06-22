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
    private $defaultClassName = ListenerProvider::class;

    /**
     * @var array
     */
    protected $defaultListenerConfig = [];

    /**
     * @var array
     */
    protected $defaultAggregateListenerConfig = [];

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
        if (!is_a($className, ListenerProviderInterface::class, true)) {
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
            $this->registerEventListeners(
                $container,
                $listenerProvider,
                array_replace_recursive($this->defaultListenerConfig, $options['listeners'] ?? []),
                array_replace_recursive($this->defaultAggregateListenerConfig, $options['aggregate_listeners'] ?? []),
                $requestedName
            );
        }

        return $listenerProvider;
    }

    /**
     * Register a collection of event listeners with the provided $listenerProvider using $eventListenerConfig.
     *
     * @param ContainerInterface        $container
     * @param AddListenerAwareInterface $listenerProvider
     * @param array                     $listenerConfig
     * @param array                     $aggregateListenerConfig
     * @param string                    $requestedName
     *
     * @throws ServiceNotCreatedException
     */
    protected function registerEventListeners(
        ContainerInterface $container,
        AddListenerAwareInterface $listenerProvider,
        array $listenerConfig,
        array $aggregateListenerConfig,
        string $requestedName
    ): void {
        try {
            if (! empty($listenerConfig)) {
                $this->registerCallableListeners(
                    $container,
                    $listenerProvider,
                    $listenerConfig,
                    $requestedName
                );
            }

            if (! empty($aggregateListenerConfig)) {
                $this->registerAggregateListeners(
                    $container,
                    $listenerProvider,
                    $aggregateListenerConfig,
                    $requestedName
                );
            }
        } catch (ServiceNotCreatedException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ServiceNotCreatedException(
                sprintf('Failed to register event listeners: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
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
     * @param array                     $aggregateListenerConfig
     * @param string                    $requestedName
     */
    public function registerAggregateListeners(
        ContainerInterface $container,
        AddListenerAwareInterface $listenerProvider,
        $aggregateListenerConfig,
        string $requestedName
    ): void {
        foreach ($aggregateListenerConfig as $index => $aggregateListener) {
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
