<?php

namespace Nechitart\AgeVerification\Model;

use Nechitart\AgeVerification\Api\Data\ValidationResponseInterface;

class ValidationResponse implements ValidationResponseInterface
{
    protected $isValid;
    protected $errors;

    public function getIsValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): ValidationResponseInterface
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors ?? [];
    }

    public function setErrors(array $errors): ValidationResponseInterface
    {
        $this->errors = $errors;

        return $this;
    }
}
