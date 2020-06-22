<?php

declare(strict_types=1);

namespace Arp\LaminasEntity;

use Arp\DoctrineEntityRepository\Persistence\CascadeSaveService;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\CascadeSaveListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\ClearListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateCreatedListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateDeletedListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateTimeListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateUpdatedListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\EntityValidationListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\ErrorListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\FlushListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\HardDeleteListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\PersistListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\SoftDeleteListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\TransactionListener;
use Arp\DoctrineEntityRepository\Persistence\PersistService;
use Arp\DoctrineEntityRepository\Query\QueryService;
use Arp\EventDispatcher\Listener\ListenerProvider;
use Arp\EventDispatcher\Resolver\EventNameResolver;
use Arp\LaminasEntity\Factory\Hydrator\EntityHydratorFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\CascadeSaveListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\ClearListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\DateCreatedListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\DateDeletedListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\DateTimeListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\DateUpdatedListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\EntityListenerProviderFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\EntityValidationListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\ErrorListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\FlushListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\HardDeleteListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\PersistListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\SoftDeleteListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Event\Listener\TransactionListenerFactory;
use Arp\LaminasEntity\Factory\Repository\Persistence\CascadeSaveServiceFactory;
use Arp\LaminasEntity\Factory\Repository\Persistence\PersistServiceFactory;
use Arp\LaminasEntity\Factory\Repository\Query\QueryServiceFactory;
use Arp\LaminasEntity\Factory\Service\EntityRepositoryFactoryFactory;
use Arp\LaminasEntity\Factory\Service\EntityRepositoryManagerFactory;
use Arp\LaminasEntity\Factory\Validator\IsEntityValidatorFactory;
use Arp\LaminasEntity\Factory\Validator\IsNotEntityValidatorFactory;
use Arp\LaminasEntity\Hydrator\EntityHydrator;
use Arp\LaminasEntity\Service\EntityRepositoryFactory;
use Arp\LaminasEntity\Service\EntityRepositoryManager;
use Arp\LaminasEntity\Validator\IsEntityValidator;
use Arp\LaminasEntity\Validator\IsNotEntityValidator;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'arp' => [
        'validators' => [

        ],
        'hydrators' => [
            EntityHydrator::class => [
                'entity_manager'  => 'doctrine.entitymanager.orm_default',
                'naming_strategy' => UnderscoreNamingStrategy::class,
                'by_value' => false,
            ],
        ],
    ],
    'entity_repository_manager' => [
        'factories' => [

        ],
    ],
    'service_manager' => [
        'factories' => [
            // Services
            EntityRepositoryManager::class => EntityRepositoryManagerFactory::class,
            EntityRepositoryFactory::class => EntityRepositoryFactoryFactory::class,

            QueryService::class             => QueryServiceFactory::class,
            PersistService::class           => PersistServiceFactory::class,
            ListenerProvider::class         => EntityListenerProviderFactory::class,
            EventNameResolver::class        => InvokableFactory::class,
            CascadeSaveService::class       => CascadeSaveServiceFactory::class,

            // Listeners
            EntityValidationListener::class => EntityValidationListenerFactory::class,
            TransactionListener::class      => TransactionListenerFactory::class,
            ErrorListener::class            => ErrorListenerFactory::class,
            PersistListener::class          => PersistListenerFactory::class,
            FlushListener::class            => FlushListenerFactory::class,
            ClearListener::class            => ClearListenerFactory::class,
            HardDeleteListener::class       => HardDeleteListenerFactory::class,
            SoftDeleteListener::class       => SoftDeleteListenerFactory::class,
            CascadeSaveListener::class      => CascadeSaveListenerFactory::class,

            DateTimeListener::class    => DateTimeListenerFactory::class,
            DateCreatedListener::class => DateCreatedListenerFactory::class,
            DateUpdatedListener::class => DateUpdatedListenerFactory::class,
            DateDeletedListener::class => DateDeletedListenerFactory::class,

            UnderscoreNamingStrategy::class => InvokableFactory::class,
        ],
    ],

    'validators' => [
        'aliases'   => [
            'isEntity' => IsEntityValidator::class,
            'isNotEntity' => IsNotEntityValidator::class,
        ],
        'factories' => [
            IsEntityValidator::class => IsEntityValidatorFactory::class,
            IsNotEntityValidator::class => IsNotEntityValidatorFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
            EntityHydrator::class => EntityHydratorFactory::class,
        ],
    ],
];
