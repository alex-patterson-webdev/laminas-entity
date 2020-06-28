<?php

declare(strict_types=1);

namespace ArpTest\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Arp\Entity\EntityInterface;
use Arp\LaminasEntity\Service\EntityRepositoryManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\LaminasEntity\Service
 */
final class EntityRepositoryManagerTest extends TestCase
{
    /**
     * @var ContainerInterface|MockObject
     */
    private $container;

    /**
     * Prepare the test case dependencies.
     */
    public function setUp(): void
    {
        $this->container = $this->getMockForAbstractClass(ContainerInterface::class);
    }

    /**
     * Assert that the EntityRepositoryManager is an instance of ContainerInterface.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryManager
     */
    public function testImplementContainerInterface(): void
    {
        $manager = new EntityRepositoryManager($this->container);

        $this->assertInstanceOf(ContainerInterface::class, $manager);
    }

    /**
     * Assert that the EntityRepositoryManager is an instance of EntityRepositoryProviderInterface.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryManager
     */
    public function testImplementsEntityRepositoryProviderInterface(): void
    {
        $manager = new EntityRepositoryManager($this->container);

        $this->assertInstanceOf(EntityRepositoryProviderInterface::class, $manager);
    }

    /**
     * Assert that the call to hasRepository() with a non-existing entity name will result in boolean false
     * being returned.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryManager::getRepository
     */
    public function testHasRepositoryWillReturnFalseForNotExistingRepository(): void
    {
        $entityName = EntityInterface::class;
        $config = [
            'services' => []
        ];

        $manager = new EntityRepositoryManager($this->container, $config);

        $this->assertFalse($manager->hasRepository($entityName));
    }

    /**
     * Assert that the call to hasRepository() with a non-existing entity name will result in boolean false
     * being returned.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryManager::getRepository
     */
    public function testGetRepositoryWillReturnTrueFromExistingRepository(): void
    {
        /** @var EntityRepositoryInterface|MockObject $repository */
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $entityName = EntityInterface::class;

        $config = [
            'services' => [
                EntityInterface::class => $repository,
            ]
        ];

        $manager = new EntityRepositoryManager($this->container, $config);

        $this->assertTrue($manager->hasRepository($entityName));
    }
}
