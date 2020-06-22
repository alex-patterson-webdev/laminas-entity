<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DoctrineEntityRepository\Persistence\Event\Listener\SoftDeleteListener;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
final class SoftDeleteListenerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return SoftDeleteListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SoftDeleteListener
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $logger = $options['logger'] ?? NullLogger::class;

        if (is_string($logger)) {
            $logger = $this->getService($container, $logger, $requestedName);
        }

        /** @var LoggerInterface $logger */
        return new SoftDeleteListener($logger);
    }
}
