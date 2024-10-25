<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser;

use PrinsFrank\PdfParser\Document\Generic\Parsing\RollingCharBuffer;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use RuntimeException;

class Pdf {
    /** @var resource */
    private readonly mixed $handle;

    /** @param resource $handle */
    final private function __construct(mixed $handle) {
        if (is_resource($handle) === false || in_array(get_resource_type($handle), ['stream'], true) === false) {
            throw new InvalidArgumentException(sprintf('$handle should be a stream, %s given', is_resource($handle) ? get_resource_type($handle) : gettype($handle)));
        }

        $this->handle = $handle;
    }

    public static function open(string $path): self {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException(sprintf('Failed to open file at path "%s"', $path));
        }

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

    public function strpos(string $needle, int $offset): ?int {
        $rollingCharBuffer = new RollingCharBuffer($needleLength = strlen($needle));
        fseek($this->handle, $offset);
        $currentCharPos = $offset;
        while (feof($this->handle) === false) {
            $rollingCharBuffer->next()->setCharacter(fgetc($this->handle));
            $currentCharPos++;
            if ($rollingCharBuffer->seenString($needle)) {
                return $currentCharPos - $needleLength;
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

    public function __destruct() {
        fclose($this->handle);
    }
}