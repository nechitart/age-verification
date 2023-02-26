<?php

namespace Nechitart\AgeVerification\Api;

use Nechitart\AgeVerification\Api\Data\ValidationResponseInterface;

interface ValidationInterface
{
    /**
     * @param string[] $blocks
     * @return ValidationResponseInterface
     */
    public function validate(array $blocks): ?ValidationResponseInterface;
}
