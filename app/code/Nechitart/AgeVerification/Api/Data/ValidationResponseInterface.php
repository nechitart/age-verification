<?php

namespace Nechitart\AgeVerification\Api\Data;

interface ValidationResponseInterface
{
    /**
     * @return bool
     */
    public function getIsValid(): bool;

    /**
     * @param bool $isValid
     * @return self
     */
    public function setIsValid(bool $isValid): self;

    /**
     * @return string[]
     */
    public function getErrors(): array;

    /**
     * @param array $errors
     * @return self
     */
    public function setErrors(array $errors): self;
}
