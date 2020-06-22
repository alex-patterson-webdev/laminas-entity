<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DateTime\DateTimeFactory;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateDeletedListener;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
final class DateDeletedListenerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return DateDeletedListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DateDeletedListener
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $dateTimeFactory = $options['date_time_factory'] ?? DateTimeFactory::class;
        $logger = $options['logger'] ?? NullLogger::class;

        $dateTimeFactory = $this->getService($container, $dateTimeFactory, $requestedName);

        if (is_string($logger)) {
            $logger = $this->getService($container, $logger, $requestedName);
        }

        return new DateDeletedListener($dateTimeFactory, $logger);
    }
}
