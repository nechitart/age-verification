<?php
declare(strict_types=1);

namespace Nechitart\AgeVerification\Controller\Customer;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Nechitart\AgeVerification\Block\Form\Edit;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;

class EditPost extends Action
{
    protected $formKeyValidator;
    protected $validator;
    protected $customerFactory;
    protected $session;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        ManagerInterface $messageManager,
        Validation $validator,
        CustomerFactory $customerFactory,
        Session $session
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $messageManager;
        $this->validator = $validator;
        $this->customerFactory = $customerFactory;
        $this->session = $session;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        $isValidRequest = $validFormKey && $this->getRequest()->isPost();
        if (!$isValidRequest || !$this->checkBlocks()) {
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }

        $block = $this->prepareVerificationBlock();
        $isBlockValid = $this->validator->validate($block);
        if (!$isBlockValid) {
            $resultRedirect->setPath('*/*/index');
            $this->addErrorLog();

            return $resultRedirect;
        }

        $this->saveCustomer((int)$this->session->getCustomerId(), $block);
        $this->messageManager->addSuccess('You successfully saved ID block');
        $resultRedirect->setPath('*/*/index');

        return $resultRedirect;
    }

    protected function saveCustomer(int $customerId, string $block)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $customer
            ->setData(View::VERIFICATION_BLOCK_ATTR_NAME, $block)
            ->setData(View::IS_VERIFIED_ATTR_NAME, true);
        $customer->save();
    }

    protected function addErrorLog()
    {
        foreach ($this->validator->getErrorLog() as $error) {
            $this->messageManager->addErrorMessage($error);
        }
    }

    protected function checkBlocks(): bool
    {
        $data = $this->getRequest()->getParams();
        $blocks = $data['customer'] ?? null;
        if (count(array_filter($blocks)) !== Edit::SEPARATED_BLOCKS_QTY) {
            $this->messageManager->addErrorMessage('All blocks should be filled');
            return false;
        }

        return true;
    }

    protected function prepareVerificationBlock(): string
    {
        $block = '';
        $data = $this->getRequest()->getParams();
        $blocks = $data['customer'];
        foreach ($blocks as $item) {
            $block .= $item;
            if (end($blocks) !== $item) {
                $block .= '-';
            }
        }

        return $block;
    }
}
