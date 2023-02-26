<?php

namespace Nechitart\AgeVerification\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Sender extends AbstractHelper
{
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    protected $config;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        Config $config
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->config = $config;
        parent::__construct($context);
    }

    public function sendEmail(string $toEmail)
    {
        try {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->config->getCustomerEmailTemplateId())
                ->setTemplateOptions($this->getTempalteOptions())
                ->setTemplateVars([])
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }

    protected function getTempalteOptions()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }
}
