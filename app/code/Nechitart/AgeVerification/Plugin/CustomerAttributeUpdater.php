<?php

namespace Nechitart\AgeVerification\Plugin;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Nechitart\AgeVerification\Model\Config;
use Magento\Framework\App\RequestInterface;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Magento\Framework\Message\ManagerInterface;

class CustomerAttributeUpdater
{
    const SEPARATED_BLOCKS_QTY = 4;
    const CUSTOMER_DATA_FIELD = 'customer';

    protected $request;
    protected $validator;
    protected $messageManager;
    protected $config;

    public function __construct(
        RequestInterface $request,
        Validation $validator,
        ManagerInterface $messageManager,
        Config $config
    ) {
       $this->request = $request;
       $this->validator = $validator;
       $this->messageManager = $messageManager;
       $this->config = $config;
    }

    public function beforeExecute()
    {
        if (!$this->config->getIsEnable()) {
            return;
        }

        $data = $this->request->getPostValue();
        $block = $this->prepareBlockParam($data[self::CUSTOMER_DATA_FIELD]);

        if (trim($block) === '') {
            $this->setVerificationToRequest($data, '');
            return;
        }

        if (trim($block) !== '' && !$this->validator->validate($block)) {
            $this->displayErrors($this->validator->getErrorLog());
            return;
        }

        $this->setVerificationToRequest($data, $block ?? '');
    }

    protected function prepareBlockParam(&$data): string
    {
        $blocks = [];
        for ($i = 0; $i < self::SEPARATED_BLOCKS_QTY; $i++) {
            $blockName = View::VERIFICATION_BLOCK_ATTR_NAME . '-' . $i;
            if (!isset($data[$blockName]) || trim($data[$blockName]) === '') {
                return '';
            }

            $blocks[] = $data[$blockName];
            unset($data[$blockName]);
        }

        return implode('-', $blocks);
    }

    protected function setVerificationToRequest($data, $value)
    {
        $data[self::CUSTOMER_DATA_FIELD][View::VERIFICATION_BLOCK_ATTR_NAME] = $value;
        $this->request->setPostValue($data);
    }

    protected function displayErrors($errors)
    {
        foreach ($errors as $error) {
            $this->messageManager->addError(__($error));
        }
    }
}
