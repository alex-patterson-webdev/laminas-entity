<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DoctrineEntityRepository\Persistence\Event\Listener\TransactionListener;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Factory\Repository\Event\Listener
 */
final class TransactionListenerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TransactionListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransactionListener
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        /** @var LoggerInterface|string $logger */
        $logger = $this->getService(
            $container,
            $options['logger'] ?? NullLogger::class,
            $requestedName
        );

        return new TransactionListener($logger);
    }
}
