<?php

namespace Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Backend\Block\Template;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Controller\RegistryConstants;

class View extends Template implements TabInterface
{
    const IS_VERIFIED_ATTR_NAME = 'is_verified';
    const VERIFICATION_BLOCK_ATTR_NAME = 'verification_block';
    const SEPARATED_BLOCKS_QTY = 5;

    protected $coreRegistry;
    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerRepository = $customerRepository;

        parent::__construct($context, $data);
    }

    public function getCustomerId(): ?int
    {
        return $this->coreRegistry
                ->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getTabLabel(): string
    {
        return __('Age Verification');
    }

    public function getTabTitle(): string
    {
        return __('Age Verification');
    }

    public function getTabClass(): string
    {
        return '';
    }

    public function getTabUrl(): string
    {
        return '';
    }

    public function isAjaxLoaded(): string
    {
        return false;
    }

    public function canShowTab(): bool
    {
        return $this->getCustomerId() ?? false;
    }

    public function isHidden(): bool
    {
        return $this->getCustomerId() ?? false;
    }

    public function getVerificationBlocks(): array
    {
        $block = $this->getCustomerAttribute(self::VERIFICATION_BLOCK_ATTR_NAME) ?? '';
        $separatedBlock = explode('-', $block);
        if (count($separatedBlock) !== self::SEPARATED_BLOCKS_QTY) {
            return array_fill(0, self::SEPARATED_BLOCKS_QTY, '');
        }

        return $separatedBlock;
    }

    public function getIsVerified(): ?bool
    {
        return $this->getCustomerAttribute(self::IS_VERIFIED_ATTR_NAME);
    }

    protected function getCustomerAttribute($name)
    {
        $attribute = $this->customerRepository->getById($this->getCustomerId())->getCustomAttribute($name);

        return $attribute ? $attribute->getValue() : null;
    }
}
