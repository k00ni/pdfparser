<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Version;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name\NameValue;
use RuntimeException;

/** @source 6. Version Designations */
enum Version: string implements NameValue {
    case V1_0 = '1.0';
    case V1_1 = '1.1';
    case V1_2 = '1.2';
    case V1_3 = '1.3';
    case V1_4 = '1.4';
    case V1_5 = '1.5';
    case V1_6 = '1.6';
    case V1_7 = '1.7';

    public static function length(): int {
        $lengths = array_unique(array_map(static function (self $case) {return strlen($case->value);}, self::cases()));

        return count($lengths) === 1 && array_key_exists(0, $lengths)
            ? $lengths[0]
            : throw new RuntimeException('Not all version numbers have an equal length');
    }
}
