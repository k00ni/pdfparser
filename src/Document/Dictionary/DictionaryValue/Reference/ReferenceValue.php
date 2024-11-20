<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\InvalidDictionaryValueTypeFormatException;

class ReferenceValue implements DictionaryValue {
    public function __construct(
        public readonly int $objectNumber,
        public readonly int $versionNumber
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return preg_match('/^[0-9]+ [0-9]+ R$/', $value) === 1;
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        $referenceParts = explode(' ', $valueString);
        if (count($referenceParts) !== 3) {
            throw new InvalidDictionaryValueTypeFormatException('Invalid valueString, expected 3 parts: "' . $valueString . '"');
        }

        if ($referenceParts[2] !== 'R') {
            throw new InvalidDictionaryValueTypeFormatException('Invalid valueString, should end with "R": "' . $valueString . '"');
        }

        $referenceObjectNumberAsInt = (int) $referenceParts[0];
        if ((string) $referenceObjectNumberAsInt !== $referenceParts[0]) {
            throw new InvalidDictionaryValueTypeFormatException('Object reference is not a valid number: "' . $referenceParts[0] . '" in reference "' . $valueString . '"');
        }

        $referenceVersionNumberAsInt = (int) $referenceParts[1];
        if ((string) $referenceVersionNumberAsInt !== $referenceParts[1]) {
            throw new InvalidDictionaryValueTypeFormatException('Object reference is not a valid number: "' . $referenceParts[0] . '" in reference "' . $valueString . '"');
        }

        return new self($referenceObjectNumberAsInt, $referenceVersionNumberAsInt);
    }
}
