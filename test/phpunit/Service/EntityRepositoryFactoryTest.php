<?php

declare(strict_types=1);

namespace ArpTest\LaminasEntity\Service;

use Arp\DoctrineEntityRepository\EntityRepositoryProviderInterface;
use Arp\LaminasEntity\Service\EntityRepositoryFactory;
use Doctrine\ORM\Repository\RepositoryFactory;
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
     * @covers \Arp\LaminasEntity\Service\EntityRepositoryFactory::getRepository
     */
    public function testImplementsRepositoryFactoryInterface(): void
    {
        $repositoryProvider = new \Arp\LaminasEntity\Service\EntityRepositoryFactory(
            $this->repositoryProvider,
            $this->repositoryFactory
        );

        $this->assertInstanceOf(RepositoryFactory::class, $repositoryProvider);
    }
}
