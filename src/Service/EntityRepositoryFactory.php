<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectRepository;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Service
 */
class EntityRepositoryFactory implements RepositoryFactory
{
    /**
     * @var EntityRepositoryProviderInterface
     */
    private EntityRepositoryProviderInterface $repositoryProvider;

    /**
     * The default (fallback) repository factory.
     *
     * @var RepositoryFactory
     */
    private RepositoryFactory $repositoryFactory;

    /**
     * @param EntityRepositoryProviderInterface $repositoryProvider
     * @param RepositoryFactory                 $repositoryFactory
     */
    public function __construct(
        EntityRepositoryProviderInterface $repositoryProvider,
        RepositoryFactory $repositoryFactory
    ) {
        $this->repositoryProvider = $repositoryProvider;
        $this->repositoryFactory = $repositoryFactory;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return EntityRepositoryInterface|ObjectRepository
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName): ObjectRepository
    {
        if ($this->repositoryProvider->has($entityName)) {
            $options = [
                'entity_name'    => $entityName,
                'entity_manager' => $entityManager,
            ];

            return $this->repositoryProvider->get($entityName, $options);
        }

        return $this->repositoryFactory->getRepository($entityManager, $entityName);
    }
}
