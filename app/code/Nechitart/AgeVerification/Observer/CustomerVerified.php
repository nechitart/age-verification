<?php

namespace Nechitart\AgeVerification\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Nechitart\AgeVerification\Model\Sender;

class CustomerVerified implements ObserverInterface
{
    const CUSTOMER_FIELD = 'customer';

    protected $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function execute(Observer $observer)
    {
        $customerEmail = $observer->getData(self::CUSTOMER_FIELD)->getEmail();

        $this->sender->sendEmail($customerEmail);
    }
}
