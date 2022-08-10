<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Generic\Character;

enum ObjectInUseOrFreeCharacter: string
{
    case IN_USE = 'n';
    case FREE   = 'f';
}
