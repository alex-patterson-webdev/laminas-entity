<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DoctrineEntityRepository\Persistence\Event\Listener\EntityValidationListener;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
final class EntityValidationListenerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return EntityValidationListener|object
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): EntityValidationListener {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        /** @var LoggerInterface|string $logger */
        $logger = $this->getService(
            $container,
            $options['logger'] ?? NullLogger::class,
            $requestedName
        );

        return new EntityValidationListener($logger);
    }
}
