<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Service;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Service
 */
interface EntityRepositoryManagerProviderInterface
{
    /**
     * Return the EntityRepositoryManager configuration array.
     *
     * @return array
     */
    public function getEntityRepositoryConfig(): array;
}
