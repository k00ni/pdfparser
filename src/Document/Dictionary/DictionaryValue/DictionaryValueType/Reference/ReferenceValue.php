<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class ReferenceValue implements DictionaryValueType
{
    public function __construct(public readonly int $objectNumber, public readonly int $versionNumber) { }

    /**
     * @throws ParseFailureException
     */
    public static function fromValue(string $valueString): DictionaryValueType
    {
        $referenceParts = explode(' ', $valueString);
        if (count($referenceParts) !== 3) {
            throw new ParseFailureException('Invalid valueString, expected 3 parts: "' . $valueString . '"');
        }

        if ($referenceParts[2] !== 'R') {
            throw new ParseFailureException('Invalid valueString, should end with "R": "' . $valueString . '"');
        }

        $referenceObjectNumberAsInt = (int) $referenceParts[0];
        if ((string) $referenceObjectNumberAsInt !== $referenceParts[0]) {
            throw new ParseFailureException('Object reference is not a valid number: "' . $referenceParts[0] . '" in reference "' . $valueString . '"');
        }

        $referenceVersionNumberAsInt = (int) $referenceParts[1];
        if ((string) $referenceVersionNumberAsInt !== $referenceParts[1]) {
            throw new ParseFailureException('Object reference is not a valid number: "' . $referenceParts[0] . '" in reference "' . $valueString . '"');
        }

        return new self($referenceObjectNumberAsInt, $referenceVersionNumberAsInt);
    }
}
