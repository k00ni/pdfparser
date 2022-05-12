<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Enum;

enum Marker: string
{
    case VERSION      = '%PDF-';
    case EOF          = '%%EOF';
    case START_XREF   = 'startxref';
    case START_STREAM = 'stream';
    case END_STREAM   = 'endstream';
}
