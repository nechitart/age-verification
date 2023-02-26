<?php

namespace Nechitart\AgeVerification\Plugin;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Nechitart\AgeVerification\Exception\InvalidValidationError;
use Nechitart\AgeVerification\Model\Config;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\ManagerInterface;

class BaseRepositoryUpdater
{
    protected $validator;
    protected $customerFactory;
    protected $config;
    protected $verificationBlock = '';
    protected $eventManager;
    protected $countriesToValidate;

    public function __construct(
        Validation $validator,
        Config $config,
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        array $countriesToValidate
    ) {
        $this->validator = $validator;
        $this->config = $config;
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->countriesToValidate = $countriesToValidate;
    }

    protected function validate(string $block)
    {
        if (!$this->validator->validate($block)) {
            throw new InvalidValidationError($this->validator->getErrorLog());
        }
    }

    protected function saveCustomer(int $customerId, string $block)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $customer
            ->setData(View::VERIFICATION_BLOCK_ATTR_NAME, $block)
            ->setData(View::IS_VERIFIED_ATTR_NAME, true);
        $customer->save();
    }

    protected function dispatchCustomerVerifiedEvent(int $customerId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $this->eventManager->dispatch(
            'customer_age_verified_after',
            ['customer' => $customer]
        );
    }

    protected function isCustomerVerified(int $customerId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $isVerified = $customer->getData(View::IS_VERIFIED_ATTR_NAME);

        return $isVerified ?? false;
    }
}
