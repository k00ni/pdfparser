<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Errors;

class Error {
    public function __construct(
        public readonly string $message
    ) {
    }
}
