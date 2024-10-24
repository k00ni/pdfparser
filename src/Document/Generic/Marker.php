<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic;

enum Marker: string {
    case VERSION = '%PDF-';
    case EOF = '%%EOF';
    case TRAILER = 'trailer';
    case XREF = 'xref';
    case START_XREF = 'startxref';
    case STREAM = 'stream';
    case END_STREAM = 'endstream';

    public function length(): int {
        return strlen($this->value);
    }
}
