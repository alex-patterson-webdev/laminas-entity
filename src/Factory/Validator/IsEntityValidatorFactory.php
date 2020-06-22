<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Validator;

use Arp\DoctrineEntityRepository\EntityRepositoryInterface;
use Arp\LaminasEntity\Service\EntityRepositoryManager;
use Arp\LaminasEntity\Validator\IsEntityValidator;
use Arp\LaminasFactory\AbstractFactory;
use Arp\LaminasFactory\Exception\ServiceNotCreatedException;
use Interop\Container\ContainerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Validator
 */
class IsEntityValidatorFactory extends AbstractFactory
{
    /**
     * @var string
     */
    protected $defaultClassName = IsEntityValidator::class;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return IsEntityValidator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? $this->getServiceOptions($container, $requestedName, 'validators');

        $className  = $options['class_name']  ?? $this->defaultClassName;
        $entityName = $options['entity_name'] ?? null;
        $fieldNames = $options['field_names'] ?? null;
        $options    = $options['options'] ?? [];

        if (null === $entityName || ! is_string($entityName)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'The required \'entity_name\' configuration option is missing or invalid for service \'%s\'',
                    $requestedName
                )
            );
        }

        /** @var EntityRepositoryInterface $repository */
        $repository = $this->getService(
            $container->get(EntityRepositoryManager::class),
            $entityName,
            $requestedName
        );

        if (empty($fieldNames)) {
            $fieldNames[] = 'id';
        }

        if (! array_key_exists('useContext', $options)) {
            $options['useContext'] = true;
        }

        return new $className($repository, $fieldNames, $options);
    }
}
