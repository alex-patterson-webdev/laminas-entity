<?php

declare(strict_types=1);

namespace Arp\LaminasEntity\Validator;

use Arp\Entity\EntityInterface;
use Arp\LaminasEntity\Constant\IsEntityError;
use Laminas\Validator\Exception\RuntimeException;

/**
 * Attempt to validate that an entity exists that matches the provided criteria.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\LaminasEntity\Validator
 */
class IsEntityValidator extends AbstractValidator
{
    /**
     * @var array
     */
    private $messageTemplates = [
        IsEntityError::NOT_FOUND
            => 'An entity of type \'%entityName%\' could not be found matching for value \'%value%\'.',
        IsEntityError::INVALID
            => 'The entity found using the provided criteria is not of type \'%entityName%\'.',
    ];

    /**
     * @var array
     */
    protected $abstractOptions = [
        'messageVariables' => [
            'entityName' => 'entityName', // Placeholder variables that can be nested in the validation error messages.
        ],
        'messages'         => [], // Array of validation failure messages
        'messageTemplates' => [], // Array of validation failure message templates

        'translator'           => null,    // Translation object to used -> Translator\TranslatorInterface
        'translatorTextDomain' => null,    // Translation text domain
        'translatorEnabled'    => true,    // Is translation enabled?
        'valueObscured'        => false,   // Flag indicating whether or not value should be obfuscated
    ];

    /**
     * Validate the entity exists matching the provided $value/$context
     *
     * @param mixed      $value   The value that should be validated.
     * @param array|null $context Optional additional data to use to perform the validation.
     *
     * @return bool
     *
     * @throws RuntimeException If the validation cannot be performed
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
                $this->error(IsEntityError::NOT_FOUND, $criteria);
                return false;
            }

            if (!$entity instanceof EntityInterface || !$entity instanceof $entityName) {
                $this->error(IsEntityError::INVALID, $criteria);
                return false;
            }

            return true;
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
