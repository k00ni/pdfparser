<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name;

use Override;

enum TrappedNameValue: string implements NameValue {
    case TRUE = 'True';
    case FALSE = 'False';
    case UNKNOWN = 'Unknown';

    #[Override]
    public static function fromValue(string $valueString): self {
        return self::from(trim(ltrim($valueString, '/')));
    }
}
