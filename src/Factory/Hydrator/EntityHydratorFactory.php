<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Hydrator;

use Arp\LaminasEntity\Hydrator\EntityHydrator;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Laminas\Hydrator\NamingStrategy\NamingStrategyEnabledInterface;
use Laminas\Hydrator\Strategy\StrategyEnabledInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Hydrator
 */
class EntityHydratorFactory extends AbstractFactory
{
    /**
     * @var string
     */
    private string $defaultClassName = EntityHydrator::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return EntityHydrator
     *
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EntityHydrator
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName, 'hydrators');

        $className = $options['class_name'] ?? $this->defaultClassName;
        $byValue = $options['by_value'] ?? false;

        $entityManager = $this->getEntityManager($container, $requestedName, $options);

        /** @var EntityHydrator $hydrator */
        $hydrator = new $className($entityManager, $byValue);

        $namingStrategy = $options['naming_strategy'] ?? null;
        if (null !== $namingStrategy && $hydrator instanceof NamingStrategyEnabledInterface) {
            if (is_string($namingStrategy)) {
                $namingStrategy = $this->getService($container, $namingStrategy, $requestedName);
            }
            $hydrator->setNamingStrategy($namingStrategy);
        }

        $strategies = $options['strategies'] ?? [];
        if (! empty($strategies) && $hydrator instanceof StrategyEnabledInterface) {
            foreach ($strategies as $name => $strategy) {
                if (is_string($strategy)) {
                    $strategy = $this->getService($container, $strategy, $requestedName);
                }
                $hydrator->addStrategy($name, $strategy);
            }
        }

        return $hydrator;
    }

    /**
     * Return the entity manager provided in configuration options.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return EntityManagerInterface
     */
    private function getEntityManager(
        ContainerInterface $container,
        string $requestedName,
        array $options
    ): EntityManagerInterface {
        $entityManager = $options['entity_manager'] ?? EntityManager::class;

        if (null === $entityManager) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'The required \'entity_manager\' configuration option is missing for service \'%s\'',
                    $requestedName
                )
            );
        }

        return $this->getService($container, $entityManager, $requestedName);
    }
}
