<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Validator;

use Arp\LaminasEntity\Constant\IsNotEntityError;
use Laminas\Validator\Exception\RuntimeException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Validator
 */
class IsNotEntityValidator extends AbstractValidator
{
    /**
     * @var array
     */
    private $messageTemplates = [
        IsNotEntityError::FOUND
            => 'An entity of type \'%entityName%\' was found matching \'%value%\'.',
    ];

    /**
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function isValid($value, array $context = []): bool
    {
        $criteria = $this->getFilterCriteria($value, $context);
        $entityName = $this->repository->getClassName();

        if (empty($criteria)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to perform validation for entity \'%s\' in \'%s\' with an empty criteria',
                    $entityName,
                    static::class
                )
            );
        }

        try {
            $entity = $this->repository->findOneBy($criteria);

            if (null === $entity) {
                return true;
            }

            $this->error(IsNotEntityError::FOUND, $criteria);
            return false;
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Failed to perform the validation for entity of type \'%s\': %s',
                    $entityName,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }
}
