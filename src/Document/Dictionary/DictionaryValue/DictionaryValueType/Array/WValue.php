<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Array;

use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class WValue extends ArrayValue {
    /** @throws InvalidArgumentException */
    public function __construct(array $value) {
        if (count($value) !== 3 || array_is_list($value) === false || is_int($value[0]) === false || is_int($value[1]) === false || is_int($value[2]) === false) {
            throw new InvalidArgumentException('Expect exactly three integer values');
        }

        parent::__construct($value);
    }

    public function getLengthRecord1InBytes(): int {
        return $this->value[0];
    }

    public function getLengthRecord2InBytes(): int {
        return $this->value[1];
    }

    public function getLengthRecord3InBytes(): int {
        return $this->value[2];
    }

    public function getTotalLengthInBytes(): int {
        return $this->value[0] + $this->value[1] + $this->value[2];
    }
}
