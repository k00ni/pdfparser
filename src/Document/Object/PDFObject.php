<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;

class PDFObject
{
    public readonly string     $content;
    public readonly Dictionary $dictionary;
    public readonly ?string    $decodedStream;

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setDictionary(Dictionary $dictionary): self
    {
        $this->dictionary = $dictionary;

        return $this;
    }

    public function setDecodedStream(?string $decodedStream): self
    {
        $this->decodedStream = $decodedStream;

        return $this;
    }
}
