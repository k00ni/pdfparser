<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Errors;

class ErrorCollection
{
    /** @var array<Error> */
    private array $errors;

    public function addError(Error|string $error): self
    {
        $this->errors[] = $error instanceof Error ? $error : new Error($error);

        return $this;
    }

    /** @return array<Error> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
