<?php

namespace Nechitart\AgeVerification\Model\VerificationBlock;

use Exception;
use Nechitart\AgeVerification\Model\Config;

class Validation
{
    const CHARS_QTY_RULE_FIELD = 'chars_qty';
    const VALID_SYMBOLS_RULE_FIELD = 'valid_symbols';
    const IS_TOTAL_RULE_FIELD = 'is_total';
    const DOB_CHECK_RULE_FIELD = 'check_dob';

    const BLOCK_QTY_ERROR = 'Block X length must be ';
    const BLOCK_NUMBERS_ERROR = 'Block X must be numeric';
    const INCORRECT_BLOCK_ERROR = 'Block X is incorrect';
    const DOB_ERROR = 'Incorrect birthday';

    const BLOCK_NUMBER_SUB = 'X';

    private $validationRules;
    private $checkSum = [7, 3, 1];
    private $errorLog = [];
    private $config;

    public function __construct(
        $validationRules,
        Config $config
    ) {
        $this->validationRules = $validationRules;
        $this->config = $config;
    }

    public function validate(string $block): bool
    {
        if (!$this->config->getIsEnable()) {
            return false;
        }

        $separatedBlocks = explode('-', $block);
        $isValid = true;
        $block = str_replace('-', '', $block);
        foreach ($separatedBlocks as $key => $value) {
            $rule = $this->validationRules[$key];
            $isValid = $this->validateBlock($value, $rule, $key + 1, $block);

            if (!$isValid) {
                return false;
            }
        }

        return $isValid;
    }

    public function getErrorLog(): array
    {
        return $this->errorLog;
    }

    protected function getMultipliedValue(string $value, int $key): int
    {
        return intval($value) * $this->checkSum[($key % 3)];
    }

    protected function addError(string $error, int $blockNumber = null, string $option = null): self
    {
        $errorMessage = str_replace(
            self::BLOCK_NUMBER_SUB,
                $blockNumber,
                $error
            );
        $errorMessage .= $option ?? '';
        $this->errorLog[] = $errorMessage;

        return $this;
    }

    protected function validateBlock(string $value, array $rule, int $blockNumber, string $block = null): bool
    {
        $isQtyValid = $this->validateSymbolsQty($value, $rule[self::CHARS_QTY_RULE_FIELD], $blockNumber);
        $isSymbolsValid = $this->validateNumeric($value, $blockNumber);
        $isDobValid = $this->checkDob($value, $rule[self::DOB_CHECK_RULE_FIELD]);
        if ($isQtyValid && $isSymbolsValid && $isDobValid) {
            return $this->validateSymbols($value, $rule[self::IS_TOTAL_RULE_FIELD], $block, $blockNumber);
        }

        return false;
    }

    protected function validateSymbolsQty(string $block, int $qty, int $blockNumber): bool
    {
        if (strlen($block) === $qty) {
            return true;
        }

        $this->addError(self::BLOCK_QTY_ERROR, $blockNumber, $qty);

        return false;
    }

    protected function validateNumeric(string $block, int $blockNumber): bool
    {
        $block = rtrim(ltrim($block));
        if (ctype_digit($block)) {
            return true;
        }

        $this->addError(self::BLOCK_NUMBERS_ERROR, $blockNumber);

        return false;
    }

    protected function validateSymbols(string $value, bool $isTotal, string $block, int $blockNumber): bool
    {
        if ($isTotal) {
            return $this->isBlockCorrect($block);
        }

        return $this->isBlockCorrect($value, $blockNumber);
    }

    protected function isBlockCorrect(string $block, int $blockNumber = null): bool
    {
        $total = 0;
        $symbols = str_split($block);
        foreach ($symbols as $key => $symbol) {
            if (count($symbols) === $key + 1) {
                break;
            }

            if (is_numeric($symbol)) {
                $total += $this->getMultipliedValue($symbol, $key);
            } else {
                $this->addError(self::INCORRECT_BLOCK_ERROR, $blockNumber);
                return false;
            }
        }

        if (($total % 10) == intval(end($symbols))) {
            return true;
        }

        $this->addError(self::INCORRECT_BLOCK_ERROR, $blockNumber);

        return false;
    }

    protected function checkDob(string $block, bool $isNeedleCheck): bool
    {
        if (!$isNeedleCheck) {
            return true;
        }

        $year = substr($block, 0, 2);
        $month = substr($block, 2, 2);
        $day = substr($block, 4, 2);
        $date = $year . '-' . $month . '-' . $day;
        try {
            $dob = new \DateTime($date);
        } catch (Exception $e) {
            $this->addError(self::DOB_ERROR);

            return false;
        }

        $now = new \DateTime();
        $yearsInterval = $now->diff($dob)->y;
        $verificationAge = $this->config->getVerificationAge();

        if ($yearsInterval < $verificationAge) {
            $this->addError(self::DOB_ERROR);
        }

        return $yearsInterval >= $verificationAge;
    }
}
