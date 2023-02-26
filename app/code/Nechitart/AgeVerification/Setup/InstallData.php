<?php

namespace Nechitart\AgeVerification\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;

class InstallData implements InstallDataInterface
{
    const ATTR_NAME_FIELD = 'name';
    const ATTR_OPTIONS_FIELD = 'options';
    const ATTR_DATA_FIELD = 'data';

    const DATA_KEY_FIELD = 'key';
    const DATA_VALUE_FIELD = 'value';

    private $customerSetupFactory;
    private $attributesData;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        DataObject $attributesData
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributesData       = $attributesData;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->attributesData->getData() as $attribute) {
            $this->createAttribute(
                $setup,
                $attribute->getData(self::ATTR_NAME_FIELD),
                $attribute->getData(self::ATTR_OPTIONS_FIELD),
                $attribute->getData(self::ATTR_DATA_FIELD)
            );
        }
    }

    protected function createAttribute(
        ModuleDataSetupInterface $setup,
        string $attributeName,
        array $attributeOptions,
        array $attributeData,
        string $entityType = Customer::ENTITY
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(
            $entityType,
            $attributeName,
            $attributeOptions
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute($entityType, $attributeName);

        $attribute->setData(
            $attributeData[self::DATA_KEY_FIELD],
            $attributeData[self::DATA_VALUE_FIELD]
        );
        $attribute->save();
    }
}
