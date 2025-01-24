<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Date;

use DateTimeImmutable;
use Override;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValue;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

class DateValue implements DictionaryValue {
    public function __construct(
        public readonly ?DateTimeImmutable $value
    ) {
    }

    #[Override]
    public static function acceptsValue(string $value): bool {
        if (str_starts_with($value, '(D:') || str_starts_with($value, 'D:')) {
            return true;
        }

        if (!str_starts_with($value, '<') || !str_ends_with($value, '>')) {
            return false;
        }

        if (($decodedValue = hex2bin(substr($value, 1, -1))) === false) {
            return false;
        }

        return str_starts_with(mb_convert_encoding($decodedValue, 'UTF-8', 'UTF-16'), 'D:')
            || str_starts_with(mb_convert_encoding($decodedValue, 'UTF-8', 'UTF-16'), '(D:');
    }

    #[Override]
    public static function fromValue(string $valueString): self {
        if (str_starts_with($valueString, '<') && str_ends_with($valueString, '>')) {
            $valueString = substr($valueString, 1, -1);
            $valueString = hex2bin($valueString);
            if ($valueString === false) {
                throw new ParseFailureException();
            }

            $valueString = mb_convert_encoding($valueString, 'UTF-8', 'UTF-16');
        }

        if (str_starts_with($valueString, '(')) {
            $valueString = substr($valueString, 1);
        }

        $parsedDate = DateTimeImmutable::createFromFormat('\D\:YmdHisP', str_replace("'", '', $valueString));
        if ($parsedDate === false) {
            throw new ParseFailureException(sprintf('Unable to parse "%s" as a date value', $valueString));
        }

        return new self($parsedDate);
    }
}
