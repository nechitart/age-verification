<?php

namespace Nechitart\AgeVerification\Test\Unit\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Registry;
use Magento\Customer\Api\Data\CustomerInterface;
use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\AttributeInterface;

class ViewTest extends TestCase
{
    /** @var Registry|MockObject $registryMock */
    protected $registryMock;

    /** @var CustomerRepositoryInterface|MockObject $customerRepositoryMock */
    protected $customerRepositoryMock;

    /** @var CustomerInterface|MockObject $customerMock */
    protected $customerMock;

    /** @var View $view */
    protected $view;

    /** @var AttributeInterface|MockObject $attributeMock */
    protected $attributeMock;

    protected function setUp(): void
    {
        $helper = new ObjectManager($this);

        $this->registryMock = $this->getMockBuilder(Registry::class)->getMock();
        $this->customerRepositoryMock = $this
            ->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->customerMock = $this
            ->getMockBuilder(CustomerInterface::class)
            ->getMockForAbstractClass();
        $this->attributeMock = $this
            ->getMockBuilder(AttributeInterface::class)
            ->getMockForAbstractClass();

        $this->view = $helper->getObject(
            View::class,
            [
                'registry' => $this->registryMock,
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    /**
     * @param int|null $customerId
     * @param bool $canShowTab
     *
     * @dataProvider canShowTabDataProvider
     */
    public function testCanShowTab(?int $customerId, bool $canShowTab)
    {
        $this->registryMock
            ->expects($this->any())
            ->method('registry')
            ->with(RegistryConstants::CURRENT_CUSTOMER_ID)
            ->willReturn($customerId);

        self::assertEquals($canShowTab, $this->view->canShowTab());
    }

    /**
     * @param array $expected
     * @param string $block
     *
     * @dataProvider getVerificationBlocksDataProvider
     */
    public function testGetVerificationBlocks(array $expected, string $block)
    {
        $this->attributeMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn($block);
        $this->customerMock
            ->expects($this->any())
            ->method('getCustomAttribute')
            ->with(View::VERIFICATION_BLOCK_ATTR_NAME)
            ->willReturn($this->attributeMock);
        $this->customerRepositoryMock
            ->expects($this->any())
            ->method('getById')
            ->willReturn($this->customerMock);

        self::assertEquals($expected, $this->view->getVerificationBlocks());
    }

    public function getVerificationBlocksDataProvider(): array
    {
        return [
            'customer_with_verification_block' => [['1234', '1234', '1234', '1234', '1'], '1234-1234-1234-1234-1'],
            'customer_without_verification_block' => [['', '', '', '', ''], '']
        ];
    }

    public function canShowTabDataProvider(): array
    {
        return [
            'customer_id_is_provided' => [1, true],
            'customer_id_not_provided' => [null, false]
        ];
    }
}
