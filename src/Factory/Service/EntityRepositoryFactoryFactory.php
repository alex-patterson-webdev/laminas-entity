<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Service;

use Arp\LaminasEntity\Service\EntityRepositoryFactory;
use Arp\LaminasEntity\Service\EntityRepositoryManager;
use Arp\LaminasFactory\AbstractFactory;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Interop\Container\ContainerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Service
 */
final class EntityRepositoryFactoryFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return EntityRepositoryFactory
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): EntityRepositoryFactory {
        /** @var EntityRepositoryManager $repositoryManager */
        $repositoryManager = $this->getService($container, EntityRepositoryManager::class, $requestedName);

        return new EntityRepositoryFactory($repositoryManager, new DefaultRepositoryFactory());
    }
}
