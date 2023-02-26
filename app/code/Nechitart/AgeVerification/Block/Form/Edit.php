<?php
declare(strict_types=1);

namespace Nechitart\AgeVerification\Block\Form;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Edit extends Template
{
    const IS_VERIFIED_ATTR_NAME = 'is_verified';
    const VERIFICATION_BLOCK_ATTR_NAME = 'verification_block';
    const SEPARATED_BLOCKS_QTY = 5;

    protected $customerRepository;
    protected $session;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Context $context,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerRepository = $customerRepository;
        $this->session = $session;
    }

    public function getCustomerId(): ?int
    {
        return (int)$this->session->getCustomer()->getId() ?? null;
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

    protected function getCustomerAttribute($name)
    {
        $attribute = $this->customerRepository->getById($this->getCustomerId())->getCustomAttribute($name);

        return $attribute ? $attribute->getValue() : null;
    }
}
