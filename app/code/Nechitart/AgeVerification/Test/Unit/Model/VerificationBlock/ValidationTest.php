<?php

namespace Nechitart\AgeVerification\Test\Unit\Model\VerificationBlock;

use Nechitart\AgeVerification\Model\VerificationBlock\Validation;
use Nechitart\AgeVerification\Test\Unit\Model\AbstractValidationTest;

class ValidationTest extends AbstractValidationTest
{
    /**
     * @param string $block
     * @param bool $isEnable
     * @param int $verificationAge
     * @param bool $expected
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(
        string $block,
        bool $isEnable,
        int $verificationAge,
        bool $expected
    ) {
        $this->setConfigSettings($isEnable, $verificationAge);
        self::assertEquals($expected, $this->validation->validate($block));
    }

    public function validateDataProvider(): array
    {
        return [
            'disable_module_test' => ['602749852-0202033-0000000-122021161000600-4', false, 18, false],
            'invalid_birth_date' => ['6027498527-0202033-0000000-122021161000600-4', true, 25, false],
            'invalid_verification_block' => ['6027498527-0202033-00000000-122021161000600-4', true, 18, false],
            'right_verification_block' => ['6027498527-0202033-0000000-122021161000600-4', true, 18, true]
        ];
    }
}
