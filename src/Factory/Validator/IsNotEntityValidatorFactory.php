<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Factory\Validator;

use Arp\LaminasEntity\Validator\IsNotEntityValidator;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Factory\Validator
 */
final class IsNotEntityValidatorFactory extends IsEntityValidatorFactory
{
    /**
     * @var string
     */
    protected $defaultClassName = IsNotEntityValidator::class;
}
