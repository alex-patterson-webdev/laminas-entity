<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Persistence;

use Arp\DoctrineEntityRepository\Constant\ClearMode;
use Arp\DoctrineEntityRepository\Constant\EntityEventOption;
use Arp\DoctrineEntityRepository\Constant\TransactionMode;
use Arp\DoctrineEntityRepository\Persistence\CascadeSaveService;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Persistence
 */
final class CascadeSaveServiceFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return CascadeSaveService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CascadeSaveService
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        /** @var LoggerInterface|string $logger */
        $logger = $options['logger'] ?? NullLogger::class;
        if (is_string($logger)) {
            $logger = $this->getService($container, $logger, $requestedName);
        }

        return new CascadeSaveService(
            $logger,
            [
                EntityEventOption::TRANSACTION_MODE => TransactionMode::DISABLED,
                EntityEventOption::FLUSH_MODE => TransactionMode::DISABLED,
                EntityEventOption::CLEAR_MODE => ClearMode::DISABLED,
            ],
            [
                EntityEventOption::TRANSACTION_MODE => TransactionMode::DISABLED,
                EntityEventOption::FLUSH_MODE => TransactionMode::DISABLED,
                EntityEventOption::CLEAR_MODE => ClearMode::DISABLED,
            ]
        );
    }
}
