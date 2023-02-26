<?php
declare(strict_types=1);

namespace Nechitart\AdultProduct\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{
    const IS_ADULT = 'is_adult';

    protected $setupFactory;

    public function __construct(
        EavSetupFactory $setupFactory
    ) {
        $this->setupFactory = $setupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->setupFactory->create();
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::IS_ADULT,
            [
                'group' => 'General',
                'type' => 'int',
                'label' => 'Is Adult',
                'input' => 'boolean',
                'required' => false,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false
            ]
        );
    }
}
