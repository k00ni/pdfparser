<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\ObjectStream;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Object\ObjectItemCollection;

class ObjectStream {
    public readonly string               $content;
    public readonly ?string              $decodedStream;
    public readonly ObjectItemCollection $objectItemCollection;
    public readonly Dictionary           $dictionary;
    public ?int                          $objectId = null;
    public ?int                          $generationNumber = null;

    public function setContent(string $content): self {
        $this->content = $content;

        return $this;
    }

    public function setDictionary(Dictionary $dictionary): self {
        $this->dictionary = $dictionary;

        return $this;
    }

    public function setDecodedStream(?string $decodedStream): self {
        $this->decodedStream = $decodedStream;

        return $this;
    }

    public function setObjectItemCollection(ObjectItemCollection $objectItemCollection): self {
        $this->objectItemCollection = $objectItemCollection;

        return $this;
    }

    public function setObjectId(int $objectId): self {
        $this->objectId = $objectId;

        return $this;
    }

    public function setGenerationNumber(int $generationNumber): self {
        $this->generationNumber = $generationNumber;

        return $this;
    }
}
