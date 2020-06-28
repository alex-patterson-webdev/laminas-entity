<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Arp\DoctrineEntityRepository\Exception\EntityRepositoryNotFoundException;
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
     * @return bool
     */
    public function hasRepository(string $entityName): bool
    {
        return $this->has($entityName);
    }

    /**
     * @param string $entityName
     * @param array  $options
     *
     * @return EntityRepositoryInterface
     *
     * @throws EntityRepositoryNotFoundException
     */
    public function getRepository(string $entityName, array $options = []): EntityRepositoryInterface
    {
        try {
            return $this->get($entityName, $options);
        } catch (\Throwable $e) {
            throw new EntityRepositoryNotFoundException(
                sprintf('Failed to provide entity repository \'%s\': %s', $entityName, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
