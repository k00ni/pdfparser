<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document;

use PrinsFrank\PdfParser\Document\CrossReference\CrossReferenceSource;

use PrinsFrank\PdfParser\Document\Errors\ErrorCollection;
use PrinsFrank\PdfParser\Document\Object\ObjectStream\ObjectStreamCollection;
use PrinsFrank\PdfParser\Document\Page\PageCollection;
use PrinsFrank\PdfParser\Document\Trailer\Trailer;
use PrinsFrank\PdfParser\Document\Version\Version;

final class Document {
    public readonly string                 $content;
    public readonly int                    $contentLength;
    public readonly ObjectStreamCollection $objectStreamCollection;
    public readonly Version                $version;
    public readonly CrossReferenceSource   $crossReferenceSource;
    public readonly Trailer                $trailer;
    public readonly PageCollection         $pageCollection;
    public readonly ErrorCollection        $errorCollection;

    public function __construct(string $content) {
        $this->content = $content;
        $this->contentLength = strlen($content);
        $this->errorCollection = new ErrorCollection();
    }

    public function setTrailer(Trailer $trailer): self {
        $this->trailer = $trailer;

        return $this;
    }

    public function setVersion(Version $version): self {
        $this->version = $version;

        return $this;
    }

    public function setCrossReferenceSource(CrossReferenceSource $crossReferenceSource): self {
        $this->crossReferenceSource = $crossReferenceSource;

        return $this;
    }

    public function setObjectStreamCollection(ObjectStreamCollection $objectStreamCollection): self {
        $this->objectStreamCollection = $objectStreamCollection;

        return $this;
    }

    public function setPageCollection(PageCollection $pageCollection): self {
        $this->pageCollection = $pageCollection;

        return $this;
    }
}
