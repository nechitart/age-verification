<?php

namespace Nechitart\AgeVerification\Plugin;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderRepositoryUpdater extends BaseRepositoryUpdater
{
    public function beforeSave(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        if (!$this->config->getIsEnable()) {
            return;
        }

        $address = $order->getBillingAddress();
        $isValidationNeeded = !is_null($address)
            && in_array($address->getCountryId(), $this->countriesToValidate)
            && !$this->isCustomerVerified($order->getCustomerId());

        if ($isValidationNeeded) {
            $verificationBlock = $order->getData(View::VERIFICATION_BLOCK_ATTR_NAME) ?? '';
            $this->validate($verificationBlock);
            $this->verificationBlock = $verificationBlock;
        }
    }

    public function afterSave(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        if (!$this->config->getIsEnable()) {
            return;
        }

        if (trim($this->verificationBlock) != '') {
            $customerId = $order->getCustomerId();
            $this->saveCustomer($customerId, $this->verificationBlock);
            $this->dispatchCustomerVerifiedEvent($customerId);
        }
    }
}
