<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Enum\Version;

final class Document
{
    public readonly string $content;
    public readonly int    $contentLength;

    public readonly Version $version;
    public readonly Trailer $trailer;

    public function __construct(string $content)
    {
        $this->content       = $content;
        $this->contentLength = strlen($content);
    }

    public function setTrailer(Trailer $trailer): void
    {
        $this->trailer = $trailer;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }
}
