<?php

namespace Nechitart\AgeVerification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BaseOption implements ArrayInterface
{
    protected $options = [];

    public function toOptionArray(): array
    {
        $localizedOptions = [];
        foreach ($this->options as $option) {
            $option['label'] = __($option['label']);
            $localizedOptions[] = $option;
        }

        return $localizedOptions;
    }
}
