<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\RuntimeException;
use PrinsFrank\PdfParser\Stream;
use PrinsFrank\PdfParser\PdfParser;

#[CoversNothing]
class ParsedResultTest extends TestCase {
    private const SAMPLES_SOURCE = '/vendor/prinsfrank/pdf-samples/';

    #[DataProvider('externalSamples')]
    public function testExternalSourcePDFs(string $pdfPath, ?string $password, Version $version, array $expectedPages): void {
        $parser = new PdfParser();

        $document = $parser->parse(Stream::openFile($pdfPath));
        $document->getCatalog();
        $document->getInformationDictionary();
        $this->addToAssertionCount(1);
    }

    /** @return iterable<string, array{0: string, 1: string, 2: array}> */
    public static function externalSamples(): iterable {
        $fileInfo = file_get_contents(dirname(__DIR__, 2) . self::SAMPLES_SOURCE . 'files.json');
        if ($fileInfo === false) {
            throw new RuntimeException('Unable to load file information from samples source. Should a \'composer install\' be run?');
        }

        $fileInfoArray = json_decode($fileInfo, flags: JSON_THROW_ON_ERROR);
        foreach ($fileInfoArray as $fileInfo) {
            yield $fileInfo->filename => [
                dirname(__DIR__, 2) . self::SAMPLES_SOURCE . 'files/' . $fileInfo->filename,
                $fileInfo->password,
                Version::from($fileInfo->version),
                $fileInfo->pages,
            ];
        }
    }
}
