<?php

namespace Nechitart\AgeVerification\Test\Unit\Plugin;

use Nechitart\AgeVerification\Block\Adminhtml\Customer\Edit\Tab\View;
use Nechitart\AgeVerification\Model\Config;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\PathInfoProcessorInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Nechitart\AgeVerification\Plugin\CustomerAttributeUpdater;

class CustomerAttributeUpdaterTest extends TestCase
{
    /** @var Validation|MockObject $validationMock */
    protected $validationMock;

    /** @var Http */
    protected $request;

    /** @var ManagerInterface|MockObject */
    protected $managerMock;

    /** @var Config|MockObject $configMock */
    protected $configMock;

    /** @var CustomerAttributeUpdater $testingClass */
    protected $testingClass;

    /** @var ObjectManager $helper */
    private $helper;

    protected function setUp(): void
    {
        $this->helper = new ObjectManager($this);

        $this->validationMock = $this
            ->getMockBuilder(Validation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->managerMock = $this->getMockBuilder(ManagerInterface::class)->getMockForAbstractClass();
        $this->configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $this->createRequest();

        $this->testingClass = $this->helper->getObject(CustomerAttributeUpdater::class, [
            'request' => $this->request,
            'validator' => $this->validationMock,
            'messageManager' => $this->managerMock,
            'config' => $this->configMock
        ]);
    }

    /**
     * @param array $requestData
     * @param bool $validateResult
     * @param bool $isEnable
     * @param string $expected
     *
     * @dataProvider beforeExecuteDataProvider
     */
    public function testBeforeExecute(
        array $requestData,
        bool $validateResult,
        bool $isEnable,
        string $expected
    ) {
        $this->request->setPostValue($requestData);
        $this->validationMock->expects($this->any())->method('validate')->willReturn($validateResult);
        $this->configMock->expects($this->any())->method('getIsEnable')->willReturn($isEnable);

        $this->testingClass->beforeExecute();
        $result = $this->request->getPostValue()[CustomerAttributeUpdater::CUSTOMER_DATA_FIELD];
        $verificationBlock = $result[View::VERIFICATION_BLOCK_ATTR_NAME] ?? '';

        self::assertEquals($expected, $verificationBlock);
    }

    public function beforeExecuteDataProvider(): array
    {
        return [
            'disable_module' => [
                [
                    'customer' => [
                        'verification_block-0' => '1',
                        'verification_block-1' => '1',
                        'verification_block-2' => '1',
                        'verification_block-3' => '1',
                    ]
                ],
                false,
                false,
                ''
            ],
            'not_correct_block' => [
                [
                    'customer' => [
                        'verification_block-0' => '1',
                        'verification_block-1' => '1',
                        'verification_block-2' => '1',
                        'verification_block-3' => '1',
                    ]
                ],
                false,
                true,
                ''
            ],
            'correct_block' => [
                [
                    'customer' => [
                        'verification_block-0' => '1',
                        'verification_block-1' => '1',
                        'verification_block-2' => '1',
                        'verification_block-3' => '1',
                    ]
                ],
                true,
                true,
                '1-1-1-1'
            ]
        ];
    }

    protected function createRequest()
    {
        $cookieReaderMock = $this->getMockBuilder(CookieReaderInterface::class)->getMockForAbstractClass();
        $converterMock = $this->getMockBuilder(StringUtils::class)->getMock();
        $configMock = $this->getMockBuilder(ConfigInterface::class)->getMockForAbstractClass();
        $processorMock = $this->getMockBuilder(PathInfoProcessorInterface::class)->getMockForAbstractClass();
        $managerMock = $this->getMockBuilder(ObjectManagerInterface::class)->getMockForAbstractClass();

        $this->request = $this->helper->getObject(Http::class, [
            'cookieReader' => $cookieReaderMock,
            'converter' => $converterMock,
            'routeConfig' => $configMock,
            'pathInfoProcessor' => $processorMock,
            'objectManager' => $managerMock
        ]);
    }
}
