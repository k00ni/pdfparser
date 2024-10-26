<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\Generic\Character\WhitespaceCharacter;
use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use RuntimeException;

class Stream {
    /** @var resource */
    private readonly mixed $handle;

    /** @param resource $handle */
    final private function __construct(mixed $handle) {
        if (is_resource($handle) === false || in_array(get_resource_type($handle), ['stream'], true) === false) {
            throw new InvalidArgumentException(sprintf('$handle should be a stream, %s given', is_resource($handle) ? get_resource_type($handle) : gettype($handle)));
        }

        $this->handle = $handle;
    }

    public static function openFile(string $path): self {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException(sprintf('Failed to open file at path "%s"', $path));
        }

        return new self($handle);
    }

    /** When useTemp is set to false, the string will be kept completely in memory increasing base memory footprint */
    public static function fromString(string $content, bool $useTemp = true): self {
        $handle = fopen(
            $useTemp ? 'php://temp': 'php://memory',
            'rb+'
        );
        fwrite($handle, $content);
        rewind($handle);

        return new self($handle);
    }

    public function getSizeInBytes(): int {
        $stats = fstat($this->handle);
        if ($stats === false) {
            throw new RuntimeException('Unable to retrieve file information');
        }

        return $stats['size'];
    }

    /** @param int<1, max> $nrOfBytes */
    public function read(int $from, int $nrOfBytes): string {
        if ($nrOfBytes <= 0) {
            throw new InvalidArgumentException(sprintf('$nrOfBytes must be greater than 0, %d given', $nrOfBytes));
        }

        fseek($this->handle, $from);

        return fread($this->handle, $nrOfBytes);
    }

    /** @param int<1, max> $nrOfBytes */
    public function chars(int $from, int $nrOfBytes): iterable {
        if ($nrOfBytes <= 0) {
            throw new InvalidArgumentException(sprintf('$nrOfBytes must be greater than 0, %d given', $nrOfBytes));
        }

        fseek($this->handle, $from);
        $bytesRead = 0;
        while ($bytesRead < $nrOfBytes) {
            yield fread($this->handle, 1);
            $bytesRead++;
        }
    }

    public function strpos(string $needle, int $offsetFromStart): ?int {
        $rollingCharBuffer = new RollingCharBuffer($needleLength = strlen($needle));
        while ($offsetFromStart < $this->getSizeInBytes()) {
            fseek($this->handle, $offsetFromStart);
            $character = fgetc($this->handle);
            $rollingCharBuffer->next()->setCharacter($character);
            $offsetFromStart++;
            if ($rollingCharBuffer->seenString($needle)) {
                return $offsetFromStart - $needleLength;
            }
        }

        return null;
    }

    public function strrpos(string $needle, int $offsetFromEnd): ?int {
        $rollingCharBuffer = new RollingCharBuffer(strlen($needle));
        $offsetFromEnd++;
        while (fseek($this->handle, - $offsetFromEnd, SEEK_END) !== -1) {
            $character = fgetc($this->handle);
            $rollingCharBuffer->next()->setCharacter($character);
            $offsetFromEnd++;
            if ($rollingCharBuffer->seenReverseString($needle)) {
                return $this->getSizeInBytes() - $offsetFromEnd + 1;
            }
        }

        return null;
    }

    public function getStartOfNextLine(int $byteOffset): ?int {
        $firstLineFeedPos = $this->strpos(WhitespaceCharacter::LINE_FEED->value, $byteOffset);
        $firstCarriageReturnPos = $this->strpos(WhitespaceCharacter::CARRIAGE_RETURN->value, $byteOffset);
        if ($firstLineFeedPos === null && $firstCarriageReturnPos === null) {
            return null;
        }

        if ($firstCarriageReturnPos === null) {
            return $firstLineFeedPos + 1;
        }

        if ($firstLineFeedPos === null) {
            return $firstCarriageReturnPos + 1;
        }

        return min($firstLineFeedPos, $firstCarriageReturnPos)
            + (abs($firstCarriageReturnPos - $firstLineFeedPos) === 1 ? 2 : 1); // If the CR and LF are next to each other, we need to add 2 bytes, otherwise 1
    }

    public function getEndOfCurrentLine(int $byteOffset): ?int {
        $firstLineFeedPos = $this->strpos(WhitespaceCharacter::LINE_FEED->value, $byteOffset);
        $firstCarriageReturnPos = $this->strpos(WhitespaceCharacter::CARRIAGE_RETURN->value, $byteOffset);
        if ($firstLineFeedPos === null && $firstCarriageReturnPos === null) {
            return null;
        }

        if ($firstCarriageReturnPos === null) {
            return $firstLineFeedPos;
        }

        if ($firstLineFeedPos === null) {
            return $firstCarriageReturnPos;
        }

        return min($firstLineFeedPos, $firstCarriageReturnPos);
    }

    public function __destruct() {
        fclose($this->handle);
    }
}