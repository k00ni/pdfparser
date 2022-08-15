<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic\Parsing;

use InvalidArgumentException;
use PrinsFrank\PdfParser\Document\Generic\Marker;
use PrinsFrank\PdfParser\Exception\BufferTooSmallException;

/**
 * @template TLength of int<1, max>
 */
class RollingCharBuffer
{
    /** @var TLength $length */
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
     * @var array<int<0, TLength>, string>
     */
    private array $buffer = [];

    /** @param TLength $length */
    public function __construct(int $length)
    {
        if ($length < 1) {
            throw new InvalidArgumentException('A negative or zero buffer length doesn\'t make sense, "' . $length . '" provided');
        }

        $this->length = $length;
    }

    public function next(): self
    {
        $this->currentIndex++;
        $this->setCharacter(null);

        return $this;
    }

    public function setCharacter(?string $char): self
    {
        $this->buffer[$this->currentIndex % $this->length] = $char;

        return $this;
    }

    /**
     * @throws BufferTooSmallException
     */
    public function getPreviousCharacter(int $nAgo = 1): ?string
    {
        if ($nAgo >= $this->length) {
            throw new BufferTooSmallException('Buffer length of "' . $this->length . '" configured, but character "-' . $nAgo . '" requested');
        }

        return $this->buffer[($this->currentIndex - $nAgo) % $this->length] ?? null;
    }

    public function seenMarker(Marker $marker): bool
    {
        foreach (array_reverse(str_split($marker->value)) as $index => $char) {
            $previousChar = $this->getPreviousCharacter($index);
            if ($previousChar !== $char) {
                return false;
            }
        }

        return true;
    }
}
