<?php

declare(strict_types=1);

namespace ArpTest\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Arp\Entity\EntityInterface;
use Arp\LaminasEntity\Service\EntityRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\LaminasEntity
 */
final class EntityRepositoryFactoryTest extends TestCase
{
    /**
     * @var EntityRepositoryProviderInterface|MockObject
     */
    private $repositoryProvider;

    /**
     * @var RepositoryFactory|MockObject
     */
    private $repositoryFactory;

    /**
     * Prepare the test case dependencies.
     */
    public function setUp(): void
    {
        $this->repositoryProvider = $this->getMockForAbstractClass(EntityRepositoryProviderInterface::class);

        $this->repositoryFactory = $this->getMockForAbstractClass(RepositoryFactory::class);
    }

    /**
     * Assert that the class implements RepositoryFactory.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryFactory::__construct
     */
    public function testImplementsRepositoryFactoryInterface(): void
    {
        $repositoryProvider = new EntityRepositoryFactory(
            $this->repositoryProvider,
            $this->repositoryFactory
        );

        $this->assertInstanceOf(RepositoryFactory::class, $repositoryProvider);
    }

    /**
     * Assert that if a repository is not found in the repository provider then the default repository factory
     * will be used.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryFactory::getRepository
     */
    public function testGetRepositoryWillFallbackToDefaultRepositoryFactoryIfNotFoundInRepositoryProvider(): void
    {
        $repositoryProvider = new EntityRepositoryFactory(
            $this->repositoryProvider,
            $this->repositoryFactory
        );

        $entityName = EntityInterface::class;

        $this->repositoryProvider->expects($this->once())
            ->method('has')
            ->with($entityName)
            ->willReturn(false);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);

        /** @var ObjectRepository|MockObject $repository */
        $repository = $this->getMockForAbstractClass(ObjectRepository::class);

        $this->repositoryFactory->expects($this->once())
            ->method('getRepository')
            ->with($entityManager, $entityName)
            ->willReturn($repository);

        $this->assertSame($repository, $repositoryProvider->getRepository($entityManager, $entityName));
    }

    /**
     * Assert that if a repository is not found in the repository provider then the default repository factory
     * will be used.
     *
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryFactory::getRepository
     */
    public function testGetRepositoryWillUseRepositoryProviderIfRepositoryIsFound(): void
    {
        $repositoryProvider = new EntityRepositoryFactory($this->repositoryProvider, $this->repositoryFactory);

        $entityName = EntityInterface::class;

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);

        $this->repositoryProvider->expects($this->once())
            ->method('has')
            ->with($entityName)
            ->willReturn(true);

        $options = [
            'entity_name'    => $entityName,
            'entity_manager' => $entityManager,
        ];

        /** @var ObjectRepository|MockObject $repository */
        $repository = $this->getMockForAbstractClass(ObjectRepository::class);

        $this->repositoryProvider->expects($this->once())
            ->method('get')
            ->with($entityName, $options)
            ->willReturn($repository);

        $this->assertSame($repository, $repositoryProvider->getRepository($entityManager, $entityName));
    }
}
