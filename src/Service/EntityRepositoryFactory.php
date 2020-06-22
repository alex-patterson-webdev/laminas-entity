<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
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
     * @var EntityRepositoryManager
     */
    private $entityRepositoryManager;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @param EntityRepositoryManager $entityRepositoryManager
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct(EntityRepositoryManager $entityRepositoryManager, RepositoryFactory $repositoryFactory)
    {
        $this->entityRepositoryManager = $entityRepositoryManager;
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
        if ($this->entityRepositoryManager->has($entityName)) {
            $options = [
                'entity_name' => $entityName,
                'entity_manager' => $entityManager
            ];
            return $this->entityRepositoryManager->get($entityName, $options);
        }
        return $this->repositoryFactory->getRepository($entityManager, $entityName);
    }
}
