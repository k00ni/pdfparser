<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Parser\Section;

use PrinsFrank\PdfParser\Document\Document;

interface SectionParser
{
    public static function parse(Document $document): void;
}
