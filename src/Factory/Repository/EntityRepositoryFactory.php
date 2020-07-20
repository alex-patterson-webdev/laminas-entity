<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository;

use Arp\DoctrineEntityRepository\EntityRepository;
use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\DoctrineEntityRepository\Persistence\PersistService;
use Arp\DoctrineEntityRepository\Query\QueryService;
use Arp\Entity\EntityInterface;
use Arp\EventDispatcher\Listener\ListenerProvider;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository
 */
final class EntityRepositoryFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private string $defaultClassName = EntityRepository::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return EntityRepositoryInterface
     *
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName, 'entity_repositories');

        $className = $options['class_name'] ?? null;
        $entityName = $options['entity_name'] ?? $requestedName;

        if (! is_a($entityName, EntityInterface::class, true)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'The \'entity_name\' configuration option must reference a class ' .
                    'of type \'%s\' : \'%s\' provided for service \'%s\'',
                    EntityInterface::class,
                    $entityName,
                    $requestedName
                )
            );
        }

        // Attempt to automatically find a value custom repository or otherwise fallback to the default.
        if (null === $className) {
            $customClassName = $this->generateCustomClassName($entityName);
            $className = $this->defaultClassName;

            if (null !== $customClassName && class_exists($customClassName)) {
                $className = $customClassName;
            }
        }

        if (! is_a($className, EntityRepositoryInterface::class, true)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'The \'class_name\' option must be of type \'%s\'; \'%s\' provided for entity repository \'%s\'',
                    EntityRepositoryInterface::class,
                    $className,
                    $requestedName
                )
            );
        }

        $queryService = $container->build(QueryService::class, ['entity_name' => $entityName]);

        $listenerProvider = $options['listener_provider'] ?? ListenerProvider::class;
        $persistService = $container->build(
            PersistService::class,
            ['entity_name' => $entityName, 'listener_provider' => $listenerProvider]
        );

        /** @var LoggerInterface $logger */
        $logger = $this->getService($container, $options['logger'] ?? NullLogger::class, $requestedName);

        return new $className($entityName, $queryService, $persistService, $logger);
    }

    /**
     * @todo Current logic needs rethink; reflection?
     *
     * @param string $entityName
     *
     * @return string
     */
    private function generateCustomClassName(string $entityName): string
    {
        //$reflectionClass = new \ReflectionClass($entityName);
        //$reflectionClass->getShortName();

        $parts = explode('\\', $entityName);
        $entity = array_pop($parts);

        return sprintf('%s\\Repository\\%sRepository', implode('\\', $parts), $entity);
    }
}
