<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryParseContext;

use InvalidArgumentException;

/**
 * @template TLevel of int<0, max>
 */
class NestingContext
{
    /** @var array<TLevel, DictionaryParseContext> */
    private array $nestingContext = [];

    /** @var TLevel */
    private int $currentLevel;

    /** @param TLevel $currentLevel */
    public function __construct(int $currentLevel = 0)
    {
        $this->currentLevel = $currentLevel;
    }

    /** @param TLevel $newLevel */
    public function setLevel(int $newLevel): self
    {
        if ($newLevel < 0) {
            throw new InvalidArgumentException('Level can\'t be negative, "' . $newLevel . '" provided');
        }

        $this->currentLevel = $newLevel;

        return $this;
    }

    public function incrementNesting(): self
    {
        $this->setLevel($this->currentLevel + 1);

        return $this;
    }

    public function decrementNesting(): self
    {
        $this->setLevel($this->currentLevel - 1);

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
}
