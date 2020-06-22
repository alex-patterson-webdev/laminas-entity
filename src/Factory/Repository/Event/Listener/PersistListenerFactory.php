<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DoctrineEntityRepository\Persistence\Event\Listener\PersistListener;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Interop\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
final class PersistListenerFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private $defaultClassName = PersistListener::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return PersistListener
     *
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PersistListener
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $className = $options['class_name'] ?? $this->defaultClassName;
        $logger = $options['logger'] ?? NullLogger::class;

        if (is_string($logger)) {
            $logger = $this->getService($container, $logger, $requestedName);
        }

        return new $className($logger);
    }
}
