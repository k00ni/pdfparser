<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic\Parsing;

use BackedEnum;
use InvalidArgumentException;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;

class RollingCharBuffer {
    /** @var int<1, max> $length */
    private int $length;

    /** @var int<0, max> */
    private int $currentIndex = 0;

    /**
     * Rolling buffer, where the modulo of the index is used. Fe: when writing 'a', 'b', 'c', 'd', 'e', 'f' to a buffer of length 3:
     * ['a']
     * ['a', 'b']
     * ['a', 'b', 'c']
     * ['d', 'b', 'c']
     * ['d', 'e', 'c']
     * ['d', 'e', 'f']
     *
     * @var array<int<0, max>, string>
     */
    private array $buffer = [];

    /** @phpstan-assert int<1, max> $length */
    public function __construct(int $length) {
        if ($length < 1) {
            throw new InvalidArgumentException('A negative or zero buffer length doesn\'t make sense, "' . $length . '" provided');
        }

        $this->length = $length;
    }

    public function next(string $char): self {
        $this->currentIndex++;
        $this->buffer[$this->currentIndex % $this->length] = $char;

        return $this;
    }

    public function getCurrentCharacter(): ?string {
        return $this->buffer[$this->currentIndex % $this->length] ?? null;
    }

    /** @throws BufferTooSmallException */
    public function getPreviousCharacter(int $nAgo = 1): ?string {
        if ($nAgo >= $this->length) {
            throw new BufferTooSmallException('Buffer length of "' . $this->length . '" configured, but character "-' . $nAgo . '" requested');
        }

        return $this->buffer[($this->currentIndex - $nAgo) % $this->length] ?? null;
    }

    /** @throws BufferTooSmallException */
    public function seenString(string $string): bool {
        if (strlen($string) > $this->length) {
            throw new BufferTooSmallException(sprintf('Buffer length of %d configured, but enum with length %d requested', $this->length, strlen($string)));
        }

        foreach (array_reverse(str_split($string)) as $index => $char) {
            $previousChar = $this->getPreviousCharacter($index);
            if ($previousChar !== $char) {
                return false;
            }
        }

        return true;
    }

    /** @throws BufferTooSmallException */
    public function seenReverseString(string $string): bool {
        if (strlen($string) > $this->length) {
            throw new BufferTooSmallException(sprintf('Buffer length of %d configured, but enum with length %d requested', $this->length, strlen($string)));
        }

        foreach (str_split($string) as $index => $char) {
            $previousChar = $this->getPreviousCharacter($index);
            if ($previousChar !== $char) {
                return false;
            }
        }

        return true;
    }

    /** @throws BufferTooSmallException */
    public function seenBackedEnumValue(BackedEnum $backedEnum): bool {
        return $this->seenString((string) $backedEnum->value);
    }

    /**
     * @template T of \BackedEnum
     * @param class-string<T> ...$enumClasses
     * @return T|null
     *
     * @no-named-arguments
     */
    public function getBackedEnumValue(string... $enumClasses): ?BackedEnum {
        foreach ($enumClasses as $enumClass) {
            foreach ($enumClass::cases() as $case) {
                if ($this->seenBackedEnumValue($case)) {
                    return $case;
                }
            }
        }

        return null;
    }
}
