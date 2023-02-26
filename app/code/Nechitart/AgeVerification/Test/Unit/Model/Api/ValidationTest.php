<?php

namespace Nechitart\AgeVerification\Test\Unit\Model\Api;

use Nechitart\AgeVerification\Model\ValidationResponse;
use Nechitart\AgeVerification\Test\Unit\Model\AbstractValidationTest;
use Nechitart\AgeVerification\Model\Api\Validation;
use Nechitart\AgeVerification\Model\ValidationResponseFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Nechitart\AgeVerification\Model\VerificationBlock\Validation as Validator;

class ValidationTest extends AbstractValidationTest
{
    /** @var ValidationResponseFactory|MockObject */
    protected $responseFactoryMock;

    /** @var Validation|MockObject */
    private $apiValidation;

    protected function setUp(): void
    {
        parent::setUp();

        $response = new ValidationResponse();
        $this->responseFactoryMock = $this
            ->getMockBuilder(ValidationResponseFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseFactoryMock->method('create')->willReturn($response);

        $this->apiValidation = $this->helper->getObject(
            Validation::class,
            [
                'validator' => $this->validation,
                'config' => $this->configMock,
                'responseFactory' => $this->responseFactoryMock
            ]
        );
    }

    /**
     * @param array $blocks
     * @param bool $isEnable
     * @param int $verificationAge
     * @param bool $expected
     * @param array $expectedErrors
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(
        array $blocks,
        bool $isEnable,
        int $verificationAge,
        ?bool $expected,
        ?array $expectedErrors
    ) {
        $this->setConfigSettings($isEnable, $verificationAge);
        $response = $this->apiValidation->validate($blocks);

        self::assertEquals($expected, $response ? $response->getIsValid() : null);
        self::assertEquals($expectedErrors, $response ? $response->getErrors() : null);
    }

    public function validateDataProvider(): array
    {
        return [
            'disable_module' => [
                ['6027498527', '0202033', '0000000', '122021161000600', '4'],
                false,
                18,
                null,
                null
            ],
            'right_blocks' => [
                ['6027498527', '0202033', '0000000', '122021161000600', '4'],
                true,
                18,
                true,
                []
            ],
            'invalid_dob' => [
                ['6027498527', '0202033', '0000000', '122021161000600', '4'],
                true,
                30,
                false,
                [Validator::DOB_ERROR]
            ],
            'invalid_first_block' => [
                ['6027498528', '0202033', '0000000', '122021161000600', '4'],
                true,
                18,
                false,
                ['Block 1 is incorrect']
            ],
            'invalid_second_block' => [
                ['6027498527', '02020335', '0000000', '122021161000600', '4'],
                true,
                18,
                false,
                ['Block 2 length must be 7']
            ],
            'invalid_third_block' => [
                ['6027498527', '0202033', '00000000', '122021161000600', '4'],
                true,
                18,
                false,
                ['Block 3 length must be 7']
            ],
            'invalid_third_block_symbols' => [
                ['6027498527', '0202033', '000000A', '122021161000600', '4'],
                true,
                18,
                false,
                ['Block 3 must be numeric']
            ],
            'invalid_total_sum' => [
                ['6027498527', '0202033', '0000000', '122021161000600', '5'],
                true,
                18,
                false,
                ['Block  is incorrect']
            ]
        ];
    }
}
