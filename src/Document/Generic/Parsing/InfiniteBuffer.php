<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic\Parsing;

class InfiniteBuffer
{
    private string $buffer = '';

    public function addChar(string $char): self
    {
        $this->buffer .= $char;

        return $this;
    }

    public function flush(): self
    {
        return $this->setValue('');
    }

    public function __toString(): string
    {
        return $this->buffer;
    }

    public function setValue(string $buffer): self
    {
        $this->buffer = $buffer;

        return $this;
    }
}
