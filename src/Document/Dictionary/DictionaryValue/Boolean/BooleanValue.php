<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Boolean;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;

class BooleanValue implements DictionaryValue {
    public function __construct(
        public readonly bool $value,
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        return $value === 'true' || $value === 'false';
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        if ($valueString === 'true') {
            return new self(true);
        }

        if ($valueString === 'false') {
            return new self(false);
        }

        throw new InvalidArgumentException(sprintf('"%s" is not a valid boolean value', $valueString));
    }
}
