<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Service
 */
final class EntityRepositoryManager extends AbstractPluginManager implements EntityRepositoryProviderInterface
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = EntityRepositoryInterface::class;

    /**
     * @param string $entityName
     *
     * @return EntityRepositoryInterface|null
     */
    public function getEntityRepository(string $entityName): ?EntityRepositoryInterface
    {
        try {
            return $this->get($entityName);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
