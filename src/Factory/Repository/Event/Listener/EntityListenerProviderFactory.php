<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Repository\Event\Listener;

use Arp\DoctrineEntityRepository\Constant\EntityEventName;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\CascadeSaveListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\ClearListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\DateTimeListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\EntityValidationListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\ErrorListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\FlushListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\HardDeleteListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\PersistListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\SoftDeleteListener;
use Arp\DoctrineEntityRepository\Persistence\Event\Listener\TransactionListener;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Repository\Event\Listener
 */
class EntityListenerProviderFactory extends ListenerProviderFactory
{
    /**
     * @var array
     */
    protected $defaultAggregateListenerConfig = [
        EntityValidationListener::class,
        TransactionListener::class,
        ErrorListener::class,
        DateTimeListener::class,
    ];

    /**
     * @var array
     */
    protected $defaultListenerConfig = [
        EntityEventName::CREATE => [
            1  => [
                CascadeSaveListener::class,
                PersistListener::class,
                FlushListener::class,
            ],
            -1 => [
                ClearListener::class,
            ],
        ],
        EntityEventName::UPDATE => [
            1  => [
                CascadeSaveListener::class,
                FlushListener::class,
            ],
            -1 => [
                ClearListener::class,
            ],
        ],
        EntityEventName::DELETE => [
            1  => [
                SoftDeleteListener::class,
                HardDeleteListener::class,
                FlushListener::class,
            ],
            -1 => [
                ClearListener::class,
            ],
        ],
    ];
}
