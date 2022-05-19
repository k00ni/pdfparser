<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceStream;

enum CrossReferenceStreamType: string
{
    case TYPE_LINKED_LIST_FREE_OBJECT = '00';
    case TYPE_UNCOMPRESSED_OBJECT = '01';
    case TYPE_COMPRESSED_OBJECT = '02';
}
