<?php

namespace Nechitart\AgeVerification\Plugin;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;

class AddressRepositoryUpdater extends BaseRepositoryUpdater
{
    public function beforeSave(
        AddressRepositoryInterface $subject,
        AddressInterface $address
    ) {
        if (!$this->config->getIsEnable()) {
            return;
        }

        $isValidationNeeded = in_array($address->getCountryId(), $this->countriesToValidate)
            && !$this->isCustomerVerified($address->getCustomerId());
        if ($isValidationNeeded) {
            $verificationBlock = $address->__toArray()[View::VERIFICATION_BLOCK_ATTR_NAME] ?? '';
            $this->validate($verificationBlock);
            $this->verificationBlock = $verificationBlock;
        }
    }

    public function afterSave(
        AddressRepositoryInterface $subject,
        AddressInterface $address
    ) {
        if (!$this->config->getIsEnable()) {
            return;
        }

        if (trim($this->verificationBlock) != '') {
            $customerId = $address->getCustomerId();
            $this->saveCustomer($customerId, $this->verificationBlock);
            $this->dispatchCustomerVerifiedEvent($customerId);
        }
    }
}
