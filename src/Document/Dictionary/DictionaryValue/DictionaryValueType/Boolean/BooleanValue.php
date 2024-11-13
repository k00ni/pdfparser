<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Boolean;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\DictionaryValueType;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class BooleanValue implements DictionaryValueType {
    public function __construct(
        public readonly bool $value,
    ) {
    }

    #[Override]
    public static function fromValue(string $valueString): DictionaryValueType {
        if ($valueString === 'true') {
            return new self(true);
        }

        if ($valueString === 'false') {
            return new self(false);
        }

        throw new InvalidArgumentException(sprintf('"%s" is not a valid boolean value', $valueString));
    }
}
