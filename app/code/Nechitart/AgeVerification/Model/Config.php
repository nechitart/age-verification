<?php

namespace Nechitart\AgeVerification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const IS_ENABLE_PATH = 'nechitart_ageverification/settings/enable';
    const VERIFICATION_AGE_PATH = 'nechitart_ageverification/settings/verification_age';
    const CUSTOMER_EMAIL_TEMPLATE_PATH = 'nechitart_ageverification/notifications/customer_notification';

    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getIsEnable(): bool
    {
        return $this->getConfig(self::IS_ENABLE_PATH);
    }

    public function getVerificationAge(): int
    {
        return $this->getConfig(self::VERIFICATION_AGE_PATH);
    }

    public function getCustomerEmailTemplateId(): int
    {
        return $this->getConfig(self::CUSTOMER_EMAIL_TEMPLATE_PATH);
    }

    protected function getConfig(string $path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }
}
