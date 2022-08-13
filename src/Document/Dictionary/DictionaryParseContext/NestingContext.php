<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext;

use PrinsFrank\PdfParser\Document\Generic\Parsing\InfiniteBuffer;

/**
 * @template TLevel of int<0, max>
 */
class NestingContext
{
    private ?string $currentLevel;

    /** @var array<string, DictionaryParseContext> */
    private array $nestingContext = [];

    /** @var array<string, InfiniteBuffer> */
    private array $keyBuffer = [];

    /** @var array<string, InfiniteBuffer> */
    private array $valueBuffer = [];

    public function __construct()
    {
        $this->currentLevel = null;
    }

    public function incrementNesting(): self
    {
        $this->currentLevel = (string) ($this->keyBuffer[$this->currentLevel] ?? (int) $this->currentLevel + 1);

        return $this;
    }

    public function decrementNesting(): self
    {
        foreach (array_reverse(array_keys($this->nestingContext)) as $previousLevel) {
            if ($previousLevel !== $this->currentLevel) {
                $this->currentLevel = (string) $previousLevel;

                break;
            }
        }

        return $this;
    }

    public function setContext(DictionaryParseContext $dictionaryParseContext): self
    {
        $this->nestingContext[$this->currentLevel] = $dictionaryParseContext;

        return $this;
    }

    public function getContext(): DictionaryParseContext
    {
        return $this->nestingContext[$this->currentLevel] ?? DictionaryParseContext::ROOT;
    }

    public function getKeyBuffer(): InfiniteBuffer
    {
        if (array_key_exists($this->currentLevel, $this->keyBuffer) === false) {
            $this->keyBuffer[$this->currentLevel] = new InfiniteBuffer();
        }

        return $this->keyBuffer[$this->currentLevel];
    }

    public function addToKeyBuffer(string $char): self
    {
        $this->getKeyBuffer()->addChar($char);

        return $this;
    }

    public function removeFromKeyBuffer(int $nChars = 1): self
    {
        $this->getKeyBuffer()->removeChar($nChars);

        return $this;
    }

    public function getValueBuffer(): InfiniteBuffer
    {
        if (array_key_exists($this->currentLevel, $this->valueBuffer) === false) {
            $this->valueBuffer[$this->currentLevel] = new InfiniteBuffer();
        }

        return $this->valueBuffer[$this->currentLevel];
    }

    public function addToValueBuffer(string $char): self
    {
        $this->getValueBuffer()->addChar($char);

        return $this;
    }

    public function removeFromValueBuffer(int $nChars = 1): self
    {
        $this->getValueBuffer()->removeChar($nChars);

        return $this;
    }

    /** @return array<positive-int, string> */
    public function getKeysFromRoot(): array
    {
        $keysFromRoot = [];
        foreach ($this->keyBuffer as $keyBuffer) {
            $keyBufferString = (string) $keyBuffer;
            if ($keyBufferString === '') {
                continue;
            }

            $keysFromRoot[] = $keyBufferString;
        }

        return $keysFromRoot;
    }

    public function flush(): self
    {
        ($this->valueBuffer[$this->currentLevel] ?? null)?->flush();
        ($this->keyBuffer[$this->currentLevel] ?? null)?->flush();

        return $this;
    }
}
