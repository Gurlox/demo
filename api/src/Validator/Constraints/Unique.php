<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class Unique extends Constraint
{
    public ?string $message;

    public string $class;

    public string $field;

    public function __construct(array $options)
    {
        if (!isset($options['class']) || !isset($options['field'])) {
            throw new MissingOptionsException('Some mandatory options for this validator are missing', $options);
        }
        parent::__construct($options);
        $this->message = $options['message'];
        $this->class = $options['class'];
        $this->field = $options['field'];
    }

    public function validatedBy(): string
    {
        return \get_class($this) . 'Validator';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
