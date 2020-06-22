<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Query;

use Arp\DoctrineEntityRepository\Query\QueryService;
use Arp\DoctrineEntityRepository\Query\QueryServiceInterface;
use Arp\Entity\Service\EntityManagerInterface;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Interop\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Query
 */
final class QueryServiceFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private $defaultClassName = QueryService::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return QueryServiceInterface
     *
     * @throws ServiceNotCreatedException If the query service cannot be created.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName);

        $className  = $options['class_name']  ?? $this->defaultClassName;
        $entityName = $options['entity_name'] ?? null;

        if (null === $entityName || ! is_string($entityName)) {
            throw new ServiceNotCreatedException(
                sprintf('The required \'entity_name\' configuration option is missing for service %s', $requestedName)
            );
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getService($container, 'doctrine.entitymanager.orm_default', $requestedName);

        $logger = new NullLogger();

        return new $className($entityName, $entityManager, $logger);
    }
}
