<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Enum\Version;

final class Document
{
    public readonly string $content;
    public readonly int $fileLength;

    public readonly Version $version;
    public readonly Trailer $trailer;

    public function __construct(string $content)
    {
        $this->content = $content;
        $this->fileLength = strlen($content);
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
