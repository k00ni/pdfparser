<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Name\TypeNameValue;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\DictionaryValueType\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Object\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStream;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use SebastianBergmann\Type\TypeName;

final class Document
{
    public readonly string $content;
    public readonly int    $contentLength;

    /** @var ObjectStream[] */
    public readonly array                $objectStreams;
    public readonly Version              $version;
    public readonly CrossReferenceSource $crossReferenceSource;
    public readonly Trailer              $trailer;

    /** @var array<string> */
    public array $errors = [];

    public function __construct(string $content)
    {
        $this->content       = $content;
        $this->contentLength = strlen($content);
    }

    public function setTrailer(Trailer $trailer): self
    {
        $this->trailer = $trailer;

        return $this;
    }

    public function setVersion(Version $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function setCrossReferenceSource(CrossReferenceSource $crossReferenceSource): self
    {
        $this->crossReferenceSource = $crossReferenceSource;

        return $this;
    }

    public function setObjectStreams(ObjectStream ...$objectStreams): self
    {
        $this->objectStreams = $objectStreams;

        return $this;
    }

    public function addError(string $error): self
    {
        $this->errors[] = $error;

        return $this;
    }

    /** @return array<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return array<ObjectItem>
     * @throws ParseFailureException
     */
    public function pages(): array
    {
        $xRefStreams = $this->objectStreamsByType(TypeNameValue::X_REF);
        if (count($xRefStreams) !== 1) {
            throw new ParseFailureException('Expected 1 xrefStream, "' . count($xRefStreams) . '" retrieved');
        }

        $rootObjectReference = $xRefStreams[0]->dictionary->getEntryWithKey(DictionaryKey::ROOT)?->value;
        if ($rootObjectReference instanceof ReferenceValue === false) {
            throw new ParseFailureException('Unable to retrieve ROOT crossReference');
        }

        $rootObject = $this->objectByReference($rootObjectReference);
        if ($rootObject === null) {
            throw new ParseFailureException('Root object was not found');
        }

        $pagesObjectReference = $rootObject->dictionary->getEntryWithKey(DictionaryKey::PAGES)?->value;
        if ($pagesObjectReference instanceof ReferenceValue === false) {
            throw new ParseFailureException('Pages object reference was not found');
        }

        $pagesObject = $this->objectByReference($pagesObjectReference);
        if ($pagesObject === null) {
            throw new ParseFailureException('Pages object was not found');
        }

        $pagesKids = $pagesObject->dictionary->getEntryWithKey(DictionaryKey::KIDS);
        // todo parse kids object as array of crossreferences

        var_dump($pagesKids);

        return [];
    }

    public function objectByReference(ReferenceValue $referenceValue): ?ObjectItem
    {
        foreach ($this->objectStreams as $objectStream) {
            foreach ($objectStream->objectItems as $objectItem) {
                if ($objectItem->objectId !== $referenceValue->objectNumber) {
                    continue;
                }

                return $objectItem;
            }
        }

        return null;
    }

    /** @return array<ObjectStream> */
    public function objectStreamsByType(TypeNameValue $typeNameValue): array
    {
        $objectStreams = [];
        foreach ($this->objectStreams as $objectStream) {
            if ($objectStream->dictionary->getEntryWithKey(DictionaryKey::TYPE)?->value === $typeNameValue) {
                $objectStreams[] = $objectStream;
            }
        }

        return $objectStreams;
    }
}
