<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class ReferenceValueArray implements DictionaryValueType {
    /** @var array<ReferenceValue> */
    public array $referenceValues;

    public function __construct(ReferenceValue ...$referenceValues) {
        $this->referenceValues = $referenceValues;
    }

    /** @throws InvalidDictionaryValueTypeFormatException */
    public static function fromValue(string $valueString): DictionaryValueType {
        $referenceParts = explode(' ', trim(rtrim(ltrim($valueString, '['), ']')));
        $nrOfReferenceParts = count($referenceParts);
        if ($nrOfReferenceParts % 3 !== 0) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid valueString, expected a multiple of 3 parts: "' . $valueString . '"');
        }

        $referenceValues = [];
        for ($i = 0; $i < $nrOfReferenceParts; $i += 3) {
            $referenceValues[] = ReferenceValue::fromValue($referenceParts[$i] . ' ' . $referenceParts[$i + 1] . ' ' . $referenceParts[$i + 2]);
        }

        return new self(... $referenceValues);
    }

    public function count(): int {
        return count($this->referenceValues);
    }

    public function objectNumbers(): array {
        $objectNumbers = [];
        foreach ($this->referenceValues as $referenceValue) {
            $objectNumbers[] = $referenceValue->objectNumber;
        }

        return $objectNumbers;
    }
}
