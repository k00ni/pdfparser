<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object\Item\CompressedObject;

use Override;
use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryParser;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Integer\IntegerValue;
use PrinsFrank\PdfParser\Document\Object\Item\ObjectItem;
use PrinsFrank\PdfParser\Document\Object\Item\UncompressedObject\UncompressedObject;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use PrinsFrank\PdfParser\Stream;

class CompressedObject implements ObjectItem {
    private readonly Dictionary $dictionary;

    public function __construct(
        public readonly int $objectNumber,
        public readonly UncompressedObject $storedInObject,
        public readonly int $startByteOffsetInDecodedStream,
        public readonly int $endByteOffsetInDecodedStream,
    ) {
    }

    #[Override]
    public function getDictionary(Stream $stream): ?Dictionary {
        if (isset($this->dictionary)) {
            return $this->dictionary;
        }

        $first = $this->storedInObject->getDictionary($stream)?->getValueForKey(DictionaryKey::FIRST, IntegerValue::class)
            ?? throw new RuntimeException('Expected a dictionary entry for "First", none found');
        $objectContent = Stream::fromString(
            implode(
                '',
                array_map(
                    fn (string $char) => chr((int) hexdec($char)),
                    str_split(
                        substr(
                            $this->storedInObject->getStreamContent($stream),
                            ($first->value + $this->startByteOffsetInDecodedStream) * 2,
                            ($this->endByteOffsetInDecodedStream - $this->startByteOffsetInDecodedStream) * 2
                        ),
                        2
                    )
                )
            )
        );

        return $this->dictionary = DictionaryParser::parse($objectContent, 0, $objectContent->getSizeInBytes());
    }
}
