<?php

namespace Nechitart\AgeVerification\Test\Unit\Model;

use Nechitart\AgeVerification\Model\Config;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractValidationTest extends TestCase
{
    /** @var Config|MockObject $configMock */
    protected $configMock;

    /** @var Validation|MockObject $validation */
    protected $validation;

    /** @var ObjectManager $helper */
    protected $helper;

    protected function setUp(): void
    {
        $this->helper = new ObjectManager($this);

        $scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)->getMockForAbstractClass();
        $this->configMock = $this->getMockBuilder(Config::class)->setConstructorArgs(
            ['scopeConfig' => $scopeConfigMock]
        )->getMock();

        $this->validation = $this->helper->getObject(
            Validation::class,
            [
                'validationRules' => $this->getValidationRules(),
                'config' => $this->configMock
            ]
        );
    }

    protected function setConfigSettings(bool $isEnabled, int $verificationAge)
    {
        $this->configMock->expects($this->any())->method('getIsEnable')->willReturn($isEnabled);
        $this->configMock->expects($this->any())->method('getVerificationAge')->willReturn($verificationAge);
    }

    protected function getValidationRules(): array
    {
        return [
            [
                Validation::CHARS_QTY_RULE_FIELD => 10,
                Validation::DOB_CHECK_RULE_FIELD => false,
                Validation::IS_TOTAL_RULE_FIELD => false,
            ],
            [
                Validation::CHARS_QTY_RULE_FIELD => 7,
                Validation::DOB_CHECK_RULE_FIELD => true,
                Validation::IS_TOTAL_RULE_FIELD => false,
            ],
            [
                Validation::CHARS_QTY_RULE_FIELD => 7,
                Validation::DOB_CHECK_RULE_FIELD => false,
                Validation::IS_TOTAL_RULE_FIELD => false,
            ],
            [
                Validation::CHARS_QTY_RULE_FIELD => 15,
                Validation::DOB_CHECK_RULE_FIELD => false,
                Validation::IS_TOTAL_RULE_FIELD => false,
            ],
            [
                Validation::CHARS_QTY_RULE_FIELD => 1,
                Validation::DOB_CHECK_RULE_FIELD => false,
                Validation::IS_TOTAL_RULE_FIELD => true,
            ],
        ];
    }
}
