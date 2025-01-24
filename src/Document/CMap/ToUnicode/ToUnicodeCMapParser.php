<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Stream;

class ToUnicodeCMapParser {
    public static function parse(Stream $stream, int $startOffset, int $nrOfBytes): ToUnicodeCMap {
        $beginCodeSpaceRangePos = $stream->getStartNextLineAfter(ToUnicodeCMapOperator::BeginCodeSpaceRange, $startOffset, $startOffset + $nrOfBytes)
            ?? throw new ParseFailureException(sprintf('Missing %s', ToUnicodeCMapOperator::BeginCodeSpaceRange->value));
        $endCodeSpaceRangePos = $stream->firstPos(ToUnicodeCMapOperator::EndCodeSpaceRange, $beginCodeSpaceRangePos, $startOffset + $nrOfBytes)
            ?? throw new ParseFailureException();
        if (preg_match('/^\s*<(?P<start>[0-9a-fA-F ]+)>\s*<(?P<end>[0-9a-fA-F ]+)>\s*$/', $stream->read($beginCodeSpaceRangePos, $endCodeSpaceRangePos - $beginCodeSpaceRangePos), $matchesSpaceRange) !== 1) {
            throw new ParseFailureException();
        }

        if (strlen($matchesSpaceRange['start']) !== strlen($matchesSpaceRange['end'])) {
            throw new ParseFailureException();
        }

        /** @var array<int, list<BFRange|BFChar>> $bfCharRangeInfo where the first index is used to track the position of the element in the CMap */
        $bfCharRangeInfo = [];
        $lastPos = $startOffset;
        while (($beginBFCharPos = $stream->getStartNextLineAfter(ToUnicodeCMapOperator::BeginBFChar, $lastPos, $startOffset + $nrOfBytes)) !== null) {
            $endBFCharPos = $stream->firstPos(ToUnicodeCMapOperator::EndBFChar, $beginBFCharPos, $startOffset + $nrOfBytes)
                ?? throw new ParseFailureException();
            if (preg_match_all('/^\s*<(?P<start>[0-9a-fA-F ]+)>\s*<(?P<end>[0-9a-fA-F ]+)>\s*$/m', $stream->read($beginBFCharPos, $endBFCharPos - $beginBFCharPos), $matchesBFChar, PREG_SET_ORDER) === 0) {
                throw new ParseFailureException($stream->read($beginBFCharPos, $endBFCharPos - $beginBFCharPos));
            }

            foreach ($matchesBFChar as $matchBFChar) {
                $bfCharRangeInfo[$beginBFCharPos][] = new BFChar((int) hexdec(trim($matchBFChar['start'])), (int) hexdec(trim($matchBFChar['end'])));
            }
            $lastPos = $beginBFCharPos;
        }

        $lastPos = $startOffset;
        while (($beginBFRangePos = $stream->getStartNextLineAfter(ToUnicodeCMapOperator::BeginBFRange, $lastPos, $startOffset + $nrOfBytes)) !== null) {
            $endBFRangePos = $stream->firstPos(ToUnicodeCMapOperator::EndBFRange, $beginBFRangePos, $startOffset + $nrOfBytes)
                ?? throw new ParseFailureException();
            if (preg_match_all('/^\s*<(?P<start>[0-9a-fA-F ]+)>\s*<(?P<end>[0-9a-fA-F ]+)>\s*(?P<targetString>(<[0-9a-fA-F ]+>)|(\[\s*(<[0-9a-fA-F ]+>\s*)+\]))$/m', $stream->read($beginBFRangePos, $endBFRangePos - $beginBFRangePos), $matchesBFRange, PREG_SET_ORDER) === 0) {
                throw new ParseFailureException();
            }

            foreach ($matchesBFRange as $matchBFRange) {
                $bfCharRangeInfo[$beginBFRangePos][] = new BFRange(
                    (int) hexdec(trim($matchBFRange['start'])),
                    (int) hexdec(trim($matchBFRange['end'])),
                    array_map(
                        fn (string $value) => (int) hexdec(trim($value)),
                        explode('><', rtrim(ltrim(str_replace(' ', '', $matchBFRange['targetString']), '[<'), '>]'))
                    )
                );
            }
            $lastPos = $beginBFRangePos;
        }

        ksort($bfCharRangeInfo); // Make sure that Char and Range are in order they occur in the CMap
        return new ToUnicodeCMap(
            (int) hexdec(trim($matchesSpaceRange['start'])),
            (int) hexdec(trim($matchesSpaceRange['end'])),
            ($byteSize = (strlen(trim($matchesSpaceRange['start'])) / 2)) >= 1 && is_int($byteSize) ? $byteSize : throw new ParseFailureException(sprintf('Byte size should be an integer of 1 or higher, got %s', $byteSize)),
            ...array_merge(...$bfCharRangeInfo)
        );
    }
}
