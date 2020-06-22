<?php

declare(strict_types=1);

namespace Arp\LaminasEntity;

use Arp\LaminasEntity\Service\EntityRepositoryManager;
use Arp\LaminasEntity\Service\EntityRepositoryManagerProviderInterface;
use Laminas\ModuleManager\Listener\ServiceListenerInterface;
use Laminas\ModuleManager\ModuleManager;
use Laminas\ModuleManager\ModuleManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity
 */
final class Module
{
    /**
     * @param ModuleManagerInterface|ModuleManager $moduleManager
     */
    public function init(ModuleManagerInterface $moduleManager): void
    {
        /** @var ContainerInterface $serviceManager */
        $serviceManager = $moduleManager->getEvent()->getParam('ServiceManager');

        $this->bootstrapEntityRepositoryManager($serviceManager);
    }

    /**
     * Prepare the entity repository manager.
     *
     * @param ContainerInterface $container
     */
    private function bootstrapEntityRepositoryManager(ContainerInterface $container): void
    {
        /** @var ServiceListenerInterface $serviceListener */
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            EntityRepositoryManager::class,
            'entity_repository_manager',
            EntityRepositoryManagerProviderInterface::class,
            'getEntityRepositoryConfig'
        );
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}
