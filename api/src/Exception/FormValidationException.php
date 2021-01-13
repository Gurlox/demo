<?php

namespace App\Exception;

class FormValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrorMessages(): array
    {
        return $this->errors;
    }
}
