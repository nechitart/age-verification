<?php
declare(strict_types=1);

namespace Nechitart\AdultProduct\Plugin;

use Closure;
use Exception;
use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Controller\Index\Index;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Nechitart\AdultProduct\Setup\InstallData;
use Magento\Quote\Model\Quote\Item;

class CheckoutUpdater
{
    protected $customerSession;
    protected $messageManager;
    protected $redirectFactory;
    protected $cart;
    protected $customerRepository;
    protected $productRepository;

    public function __construct(
        Session $customerSession,
        ManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        Cart $cart,
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->cart = $cart;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function aroundExecute(Index $subject, Closure $proceed)
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage('Please log in');
            return $this->redirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->isCustomerVerified() && $this->isCartWithAdultProducts()) {
            $this->messageManager->addErrorMessage('Please verify your age');
            return $this->redirectFactory->create()->setPath('checkout/cart');
        }

        return $proceed();
    }

    protected function isCustomerVerified(): bool
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        try {
            $isVerified = $customer->getCustomAttribute(View::IS_VERIFIED_ATTR_NAME);
            return $isVerified && (bool) $isVerified->getValue();
        } catch (Exception $e) {
            return false;
        }
    }

    protected function isCartWithAdultProducts(): bool
    {
        $cartItems = $this->cart->getQuote()->getAllItems();
        foreach ($cartItems as $cartItem) {
            if ($this->isProductAdult($cartItem)) {
                return true;
            }
        }

        return false;
    }

    protected function isProductAdult(Item $item): bool
    {
        $productId = (int) $item->getProduct()->getId();
        $product = $this->productRepository->getById($productId);
        $isProductAdult = $product->getCustomAttribute(InstallData::IS_ADULT);

        return $isProductAdult && $isProductAdult->getValue();
    }
}
