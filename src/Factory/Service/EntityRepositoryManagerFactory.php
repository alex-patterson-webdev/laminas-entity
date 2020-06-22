<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Service;

use Arp\LaminasEntity\Service\EntityRepositoryManager;
use Arp\LaminasFactory\AbstractFactory;
use Interop\Container\ContainerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Service
 */
final class EntityRepositoryManagerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return EntityRepositoryManager
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): EntityRepositoryManager {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $config = $options['config'] ?? [];

        return new EntityRepositoryManager($container, $config);
    }
}
