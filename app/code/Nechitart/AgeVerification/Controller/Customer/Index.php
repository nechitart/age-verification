<?php
declare(strict_types=1);

namespace Nechitart\AgeVerification\Controller\Customer;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
